<?php
// Hide Errors
error_reporting(0);

// Get Settings
require '../App/Config/config.php';
define('ANNOUNCEINTERVAL', 900);
define('MEMBERSONLY', true);

// Debug
define('DEBUG_OUTPUT', false);
define('DEBUG_CODE', true);
if (DEBUG_CODE) {
    include "../App/Helpers/exception_helper.php";
    set_exception_handler("handleUncaughtException");
}

// Debug Function
function mydebug($msg)
{
    if (is_writeable('test.txt')) {
        $fh = fopen('test.txt', 'a+');
        fputs($fh, "[Log] " . date("d.m.Y H:i:s") . " $msg\n");
        fclose($fh);
    }
}

// PDO Function
function pdo($pdo, $sql, $args = NULL)
{
    try {
        if (!$args) {
            return $pdo->query($sql);
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    } catch (\Exception $e) {
        if (DEBUG_CODE) {
            mydebug($e);
            throw $e;
        }
        die(response('Database Error'));
    }
}

// Response Fuction
function response($peer_array, $seeders = 0, $leechers = 0)
{
    if (is_string($peer_array)) {
        return 'd14:failure reason' . strlen($peer_array) . ':' . $peer_array . 'e';
    }
    $peer_list = '';
    foreach ($peer_array as $peer) {
        $pid = '';
        if (!isset($_GET['no_peer_id'])) {
            $real_id = hex2bin($peer['peer_id']);
            $pid = '7:peer id' . strlen($real_id) . ':' . $real_id;
        }
        $peer_list .= 'd2:ip' . strlen($peer['ip']) . ':' . $peer['ip'] . $pid . '4:porti' . $peer['port'] . 'ee';
    }
    $response = 'd8:intervali' . ANNOUNCEINTERVAL . 'e12:min intervali30e8:completei' . $seeders . 'e10:incompletei' . $leechers . 'e5:peersl' . $peer_list . 'ee';
    if (DEBUG_OUTPUT) {
        mydebug($response);
    }
    return $response;
}

// Set Database Connection
$opt = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
);
$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHAR;
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $opt);
} catch (\Exception $e) {
    die(response('The Database Details Are Incorrect'));
}

// Block Browsers
$agent = $_SERVER["HTTP_USER_AGENT"];
if (preg_match("/^Mozilla|^Opera|^Links|^Lynx/i", $agent)) {
    die('What Are you doing ???');
}

// Block Unwanted Clients
$stmt = pdo($pdo, "SELECT code FROM clients")->fetchAll();
$users_agent = substr($_GET['peer_id'], 0, 8);
foreach ($stmt as $bannedclient) {
    if (@strpos($users_agent, $bannedclient['code']) !== false) {
        die(response('Client is Banned'));
    }
}

// Get Client Data
foreach (['info_hash', 'peer_id', 'port', 'uploaded', 'downloaded', 'left'] as $key) {
    $client[$key] = $_GET[$key];
    if (!isset($client[$key])) {
        die(response("Missing Key - $client[$key]"));
    }
}
foreach (["info_hash", "peer_id"] as $key) {
    if (strlen($client[$key]) != 20) {
        die(response("Wrong Length " . strlen($client[$key]) . " - $client[$key]"));
    }
    $client[$key] = bin2hex($client[$key]);
}
foreach (['uploaded', 'downloaded', 'left'] as $key) {
    if (!is_numeric($client[$key]) || $client[$key] < 0) {
        die(response("Wierd Number - $client[$key]"));
    }
}
foreach (['event'] as $key) {
    $client[$key] = strtolower($_GET[$key]);
}
$client['passkey'] = $_GET['passkey'];
if (strlen($client['passkey']) != 32 && MEMBERSONLY) {
     die(response("Invalid passkey (" . strlen($client['passkey']) . " - $client[passkey])"));
}
if (!ctype_digit($client['port']) || $client['port'] < 1 || $client['port'] > 65535) {
    die(response("Invalid client port $client[port]"));
}
$client['seeder'] = ($client['left'] == 0) ? "yes" : "no";
$client['ip'] = $_SERVER["REMOTE_ADDR"] ?? $_GET['ip'];

// Get User
$user = array();
$user['id'] = 0;
if (MEMBERSONLY) {
    $stmt = pdo($pdo, "SELECT u.id, u.class, u.uploaded, u.downloaded, u.enabled, u.status, u.ip, u.passkey,  u.role_id, g.maxslots, g.group_role_id FROM users u INNER JOIN `groups` g ON u.class = g.group_id WHERE u.passkey=? LIMIT 1", [$client['passkey']])->fetch(PDO::FETCH_ASSOC);
    foreach ($stmt as $key => $value) {
        $user[$key] = $value;
    }
    $role = $user['group_role_id'] >= $user['role_id'] ? $user['group_role_id'] : $user['role_id'];
    $permission = pdo($pdo, "SELECT can_download FROM `permissions` WHERE `permission_id`=?",  [$role])->fetch(PDO::FETCH_ASSOC);
    foreach ($permission as $key => $value) {
        $user[$key] = $value;
    }
    // Check User
    if (!$user) {
        die(response("Cannot locate a user with that passkey!"));
    } elseif ($user["can_download"] == "no") {
        die(response("You do not have permission to download."));
    } elseif (!$user["passkey"] == $client['passkey']) {
        die(response("Can NOT find user passkey."));
    } elseif (!$user["enabled"] == 'yes') {
        die(response("not enabled."));
    } elseif (!$user["status"] == 'confirmed') {
        die(response("status not confirmed."));
    }
}

// Get Torrent
$torrent = pdo($pdo,  "SELECT id, info_hash, banned, freeleech, seeders + leechers AS numpeers, UNIX_TIMESTAMP(added) AS ts, seeders, leechers, times_completed FROM torrents WHERE info_hash=?", [$client['info_hash']], 'error getting torrent')->fetch(PDO::FETCH_ASSOC);
// Check Torrent
if (!$torrent) {
    die(response("Torrent not found on this hash = $client[info_hash]"));
} elseif ($torrent["banned"] == 'yes') {
    die(response("Torrent has been banned"));
}

// Get Peer
$peer = pdo($pdo, "SELECT seeder, UNIX_TIMESTAMP(last_action) AS ez, peer_id, ip, port, uploaded, downloaded, userid, passkey FROM peers WHERE torrent = ? AND peer_id = ? AND passkey = ?", [$torrent['id'], $client['peer_id'], $client['passkey']])->fetch(PDO::FETCH_ASSOC);

if (!$peer) {
    // New Peer
    if (MEMBERSONLY) {
        $slot = pdo($pdo, "SELECT DISTINCT torrent FROM peers WHERE userid =? AND seeder=?", [$user['id'], 'no'])->rowCount();
        if ($slot >= $user['maxslots']) {
            die(response("Maximum Slot exceeded! You may only download $slot torrent at a time."));
        }
        pdo($pdo, "INSERT INTO `snatched` (`uid`, `tid`, `start_time`, `upload_time`, `completed`) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE `upload_time` = ?", [$user['id'], $torrent['id'], strtotime(TT_DATE), strtotime(TT_DATE), $client['seeder'], strtotime(TT_DATE)]);
    }
    pdo($pdo, "INSERT INTO peers (connectable, torrent, peer_id, ip, passkey, port, uploaded, downloaded, to_go, started, last_action, seeder, userid, client)VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE last_action = ?", ['yes', $torrent['id'], $client['peer_id'], $client['ip'], $client['passkey'], $client['port'], $client['uploaded'], $client['downloaded'], $client['left'], TT_DATE, TT_DATE, $client['seeder'], $user['id'], $agent, TT_DATE]);
} else {
    // Update Peer
    $elapsed = ($peer['seeder'] == 'yes') ? ANNOUNCEINTERVAL - floor(($peer['ez'] - time()) / 60) : 0;
    $upthis = max(0, $client['uploaded'] - $peer["uploaded"]);
    $downthis = max(0, $client['downloaded'] - $peer["downloaded"]);
    if (($upthis > 0 || $downthis > 0 || $elapsed > 0) && (is_int($user['id']) && $user['id'] > 0)) {
        if ($torrent["freeleech"] == 1) {
            pdo($pdo, "UPDATE users SET uploaded = uploaded + ? WHERE id=?", [$upthis, $userid]);
            pdo($pdo,  "UPDATE `snatched` SET `upload` = `upload` + '$upthis', `upload_time` = ?, `last_time` = `last_time` + '$elapsed', completed = ? WHERE `tid` = ? AND `uid` = ?", [strtotime(TT_DATE), $client['seeder'], $torrent['id'], $user['id']]);
        } else {
            pdo($pdo, "UPDATE users SET uploaded = uploaded + ?, downloaded = downloaded + ? WHERE id=?", [$upthis, $downthis, $user['id']]);
            pdo($pdo, "UPDATE `snatched` SET `upload` = `upload` + '$upthis', `download` = `download` + '$downthis', `upload_time` = ?, `last_time` = `last_time` + '$elapsed', completed = ? WHERE `tid` = ? AND `uid` = ?", [strtotime(TT_DATE), $client['seeder'], $torrent['id'], $user['id']]);
        }
    }
    pdo($pdo, "UPDATE peers SET ip = ?, passkey = ?, port = ?, uploaded = ?, downloaded = ?, to_go = ?, last_action = ?, client = ?, seeder = ? WHERE torrent = ? AND peer_id = ?", [$client['ip'], $client['passkey'], $client['port'], $client['uploaded'], $client['downloaded'], $client['left'], TT_DATE, $agent, $client['seeder'], $torrent['id'], $client['peer_id']]);
}

// Event Completed
if ($client['event'] == "completed") {
    if (MEMBERSONLY) {
        pdo($pdo, "INSERT INTO completed (userid, torrentid, date) VALUES (?,?,?) ON DUPLICATE KEY UPDATE date = ?", [$user['id'], $torrent['id'], TT_DATE, TT_DATE]);
        pdo($pdo, "UPDATE `snatched`  SET `completed` = ? WHERE `tid` = ? AND `uid` = ?", [1, $torrent['id'], $user['id']]);
    }
    $torrent['times_completed']++;
}

// Event Stopped
if ($client['event'] == "stopped") {
    pdo($pdo, "DELETE FROM peers  WHERE  torrent = ? AND peer_id = ?", [$torrent['id'], $client['peer_id']]);
}

// Get Peers For Response
$all_peers = pdo($pdo, "SELECT peer_id, ip, port, seeder FROM peers  WHERE torrent = ? ORDER BY RAND() LIMIT 50", [$torrent['id']])->fetchAll();
$response_array = array();
$seeders = 0;
$leechers = 0;
foreach ($all_peers as $peer) {
    if ($peer['seeder'] == 'no') {
        $leechers++;
    } else {
        $seeders++;
    }
    $response_array[] = array('ip' => $peer['ip'], 'port' => $peer['port'], 'peer_id' => $peer['peer_id']);
}

// Update Torrent
pdo($pdo, "UPDATE torrents SET last_action = ?, leechers = ?, seeders = ?, times_completed = ?, visible = ? WHERE id=?", [TT_DATE, $leechers, $seeders, $torrent['times_completed'], 'yes', $torrent['id']]);

// Show Response
die(response($response_array, $seeders, $leechers));