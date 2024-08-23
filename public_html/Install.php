<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Template Data Array 
$data = array();

// Check Url Correct
function check_url() {
    $data['url'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $check = substr($data['url'], -1);
    if ($check == '/') {
        $data['url'] = substr($data['url'], 0, -1);
    }
    $data['full_url'] = $data['url'].'/index.php';
    setcookie("url", $data['full_url'], time() + (86400 * 30), "/");
    return $data;
}

// Check php ini Settings
function get_php_setting($val)
{
    $r = (ini_get($val) == '1' ? 1 : 0);
    return $r ? 'ON' : 'OFF';
}

// Check Server Settings
function check_enviroment() {
$php_recommended_settings = array(
    array('Safe Mode', 'safe_mode', 'OFF'),
    array('Display Errors (Can be off, but does make debugging difficult.)', 'display_errors', 'ON'),
    array('File Uploads', 'file_uploads', 'ON'),
    array('Register Globals', 'register_globals', 'OFF'),
    array('Output Buffering', 'output_buffering', 'OFF'),
    array('Session auto start', 'session.auto_start', 'OFF'),
    array('allow_url_fopen (Required for external torrents)', 'allow_url_fopen', 'ON'),
);

$version = phpversion() < '8' ? '<b><font color="red">No</font> 8 or above required</b>' : '<b><font color="green">Yes</font></b>';
$pdoext = extension_loaded('PDO') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>';
$sockets = extension_loaded('sockets') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>';
$mb = extension_loaded('mbstring') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>';
$gd = extension_loaded('gd') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>';
$zlib = extension_loaded('zlib') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>';
$xml = extension_loaded('xml') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>';
$curl = function_exists('curl_init') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>';
$openssl = extension_loaded('openssl') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>';
$bcmath = extension_loaded('bcmath') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>';

$html = "<div class='row p-3'>";
$html .= "<div class='text-center'>Extensions</div>";
$html .= "<div class='col-7'>PHP version >= 8</div>";
$html .= "<div class='col-5'>$version - Your PHP version is " . phpversion() . "</div>";
$html .= "<div class='col-7'>PDO Ext</div>";
$html .= "<div class='col-5'>$pdoext</div>";
$html .= "<div class='col-7'>sockets support</div>";
$html .= "<div class='col-5'>$sockets</div>";
$html .= "<div class='col-7'>check mb support</div>";
$html .= "<div class='col-5'>$mb</div>";
$html .= "<div class='col-7'>check gd support</div>";
$html .= "<div class='col-5'>$gd</div>";
$html .= "<div class='col-7'>zlib compression support</div>";
$html .= "<div class='col-5'>$zlib</div>";
$html .= "<div class='col-7'>XML support</div>";
$html .= "<div class='col-5'>$xml</div>";
$html .= "<div class='col-7'>curl support (Not required but external torrents may scrape faster)</div>";
$html .= "<div class='col-5'>$curl</div>";
$html .= "<div class='col-7'>openSSL (for the torrent encryption mod)</div>";
$html .= "<div class='col-5'>$openssl</div>";
$html .= "<div class='col-7'>bcmath support (Required for IPv6)</div>";
$html .= "<div class='col-5'>$bcmath</div>";
$html .= "<div class='col-7'>app/config/config.php</div>";
if (@file_exists('../config/config.php') && @is_writable(CONFIG['APPROOT'].'/config/config.php')) {
    $html .= "<div class='col-5'><b><font color='red'>Writeable</font></b><br />Warning: leaving app/config/config.php writeable is a security risk</div>";
} else {
    $html .= "<div class='col-5'><b><font color='green'>Unwriteable</font></b></div>";
}
$html .= "<div class='col-7'>Document Root<br /><i><font size='1'>(Use this for your PATHS in app/config/config.php)</font></i></div>";
$html .= "<div class='col-5'>". str_replace('\\', '/', getcwd()) ."</div>";
$html .= "</div><br>";

$html .= "<div class='row p-3'>";
$html .= "<div class='text-center'>Settings</div>";
$html .= "<div class='col-6'>Directive</div>";
$html .= "<div class='col-3'>Reccommended</div>";
$html .= "<div class='col-3'>Actual</div>";
foreach ($php_recommended_settings as $phprec) {
    $html .= "<div class='col-6'>" . $phprec[0] . ":</div>";
    $html .= "<div class='col-3'>" . $phprec[2] . ":</div>";
    if (get_php_setting($phprec[1]) == $phprec[2]) { 
        $settings = "green";
    } else {
		$settings = "red";
    }
    $html .= "<div class='col-3'><font color='$settings'>".get_php_setting($phprec[1])."</font></div>";
 }
 $html .= "</div>";

 return $html;
}

// Check DB
function check_db() {
$html = '';
try {
    $link = new PDO('mysql:host=' . $_POST['host'] . ';dbname=' . $_POST['dbname'], $_POST['dbuser'], $_POST['dbpass']);
    $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $html = "<p class='text-center'><font color='#ff0000'><b>Failed to connect to database:</b></font></p> (%d) %s<br />" . $e->getMessage();
    return $html;
}

$arr = ["addedrequests","attachments","bans","bookmarks","bonus","blocks","categories","clients","comments",
"countries","faq","forumcats","forum_posts","forum_forums","forum_readposts"
,"forum_topics","friends","groups","guests","languages","likes","log","messages","news"
,"peers","pollanswers","polls","ratings","reports","requests","rules","snatched","shoutbox","staffmessages"
,"stylesheets","tasks","teams","tmdb","torrentlang","torrents","users","warnings"];

$html .= "<p class='text-center'><b>Check SQL Mode:</b><p>";
$stmt = $link->query("SHOW VARIABLES LIKE 'sql_mode'")->fetch();
if (!$stmt) {
    $html .= "<p class='text-center'><font color='#ff0000'><b>SQL Mode:</b></font> Error Getting SQL Mode</p><br><br>";
} else {
    $html .= "<p class='text-center'><font color='#00cc00'><b>SQL Mode:</b></font> $stmt[1] </p><br><br>";
}
    
$html .= "<p class='text-center'><b>Table Status Check:</b><p>";
$html .= "<div class='row'>";
$stmt = $link->prepare("SHOW TABLES");
$stmt->execute();
if (!$stmt) {
    $html .= "<font color='#ff0000'><b>Failed to list tables:</b></font>";
} else {
    $tables = array();
    while ($rr = $stmt->fetch()) {
        $tables[] = $rr[0];
    }
    foreach ($arr as $t) {
        if (!in_array($t, $tables)) {
            $html .= "<div class='col-2'>$t</div><div class='col-2'><font color='#ff0000'><b>MISSING</b></font></div>";
        } else {
            $html .= "<div class='col-2'>$t</div><div class='col-2'><font color='green'><b>OK</b></font></div>";
        }
    }
    $html .= "</div>";
}
$link = null; 

return $html;

}

// Write config.php File
function write_config() {
$text = "<?php
define('CONFIG', array (

// DB Details
'DB_HOST' => $_POST[DB_HOST],
'DB_USER' => $_POST[DB_USER],
'DB_PASS' => $_POST[DB_PASS],
'DB_NAME' => $_POST[DB_NAME],
'DB_CHAR' => 'utf8mb4',

// URL Root
'URLROOT' => $_POST[URLROOT],

// App Root
'APPROOT' => dirname(dirname(__FILE__)),
'NAME_SPACE' => '../',

// Paths
'BACUP' => '../data/backups',
'CACHE' => '../data/cache',
'LANG' => '../data/languages';
'LOGGER' => '../data/logs',
'UPLOADDIR' => '../data/uploads',
'EMAILDIR' => '../data/email',

// Quick Time
'TT_TIME' => time(),
'TT_DATE' => date('Y-m-d H:i:s'),

// Version
'VERSION' => 'PDO',

// File Charset
'CHARSET' => 'utf-8',

// Announcelist //seperate via comma
'ANNOUNCELIST' => '/announce.php',

// Default Block Layout
'DEFAULT_LAYOUT' => false,
 
// Passkey Url
'PASSKEYURL' => '/announce.php?passkey=%s',

// Can edit Settings
'_OWNERS' => array($_POST[_OWNERS]), // Example & with more '_OWNERS' => array('mjay', 'mjay', 'mjay')

// Image upload settings
'USE_WEBP' => $_POST[USE_WEBP], // Use Webp 
'IMAGEMAXFILESIZE' => 524288, // Max uploaded image size in bytes (Default: 512 kB)
'ALLOWEDIMAGETYPES' => array(
    'image/gif' => '.gif',
    'image/jpeg' => '.jpg',
    'image/jpg' => '.jpg',
    'image/png' => '.png',
),

// Hide Blocks On Pages
'ISURL' => array('login', 'logout', 'signup', 'contact', 'recover', 'recover/confirm'),

// category,name,dl,magnet,uploader,tube,tmdb,comments,nfo,size,completed,seeders,leechers,health,external,added,speed,wait,rating
'TORRENTTABLE_COLUMNS' => 'category,name,dl,magnet,uploader,tube,tmdb,comments,nfo,size,completed,seeders,leechers,health,external,added,speed,wait,rating,admin',

// Set User Group
'_UPLOADER' => 4,
'_MODERATOR' => 5,
'_SUPERMODERATOR' => 6,
'_ADMINISTRATOR' => 7,

// Cat arrays
'PopularCats' => array(1), // popular cat id for popular block
'MovieCats' => array('Film'), // movie cats eg array('Movies','MoviesHD')
'SerieCats' => array('TV'), // serie cat eg array('Anime','tv')

// TMDB
'_TMDBAPIKEY' => $_POST[_TMDBAPIKEY], // Place Key Here ////////
'_TMDBLANG' => 'en', // TMDB Language
'_TMDBDEBUG' => false, // TMDB Debug
'_TMDBswitch' => $_POST[_TMDBswitch], // TMDB On - Off
'_OMDBAPIKEY' => $_POST[_OMDBAPIKEY], // Place Key Here ////////
'_OMDBswitch' => $_POST[_OMDBswitch], // TMDB On - Off

));";
file_put_contents('../app/config/config.php', $text);
//file_put_contents('backend/newconfig.php', $text); // for v3
}

// Get Step From Url
$step = (int) $_GET['step'] ?? 0;

if ($step == 0 || !$step) {
    // 0) check url
    $data = check_url();
    $data['instruction'] = "Welcome to install<br>Is the url correct - $data[url]<br>";
    $data['form'] = 'Url should be domain name & not have / on end';
    $data['step'] = "<a href='$data[full_url]?step=1'>Next Step 1</a>";
} elseif ($step == 1) {
    // 1) check server
    $data['instruction'] = "Please check server config<br>";
    $data['form'] = check_enviroment();
    $data['step'] = "<a href='$_COOKIE[url]?step=2'>Next Step 2</a>";
} elseif ($step == 2) {
    // 2) Enter DB Details
    $data['instruction'] = "Enter Database Details";
    $form = "<form method='post' action='$_COOKIE[url]?step=3'>";
    $form .= "<div class='row'>";
    $form .= "<div class='col'>";
    $form .= "<dl class='row mb-0'>";
    $form .= "<dt class='col-sm-5'>Host</dt><dd class='col-sm-7'><input type='text' class='form-control' name='host' placeholder='eg localhost'></dd>";
    $form .= "<dt class='col-sm-5'>DB Username</dt><dd class='col-sm-7'><input type='text' class='form-control' name='dbuser' placeholder='eg db_user_1'></dd>";
    $form .= "<dt class='col-sm-5'>DB Password</dt><dd class='col-sm-7'><input type='text' class='form-control' name='dbpass' placeholder='eg my_db_pass'></dd>";
    $form .= "<dt class='col-sm-5'>DB Name</dt><dd class='col-sm-7'><input type='text' class='form-control' name='dbname' placeholder='eg db_name'></dd>";
    $form .= "<div class='text-center'><input type=submit value='Next Step 3'></div></dd>";
    $form .= "</div>";
    $form .= "</div>";
    $form .= "</form>";
    $data['form'] = $form;
    $data['step'] = "";
} elseif ($step == 3) {
    // 3) Insert Install
    try {
        $link = new PDO('mysql:host=' . $_POST['host'] . ';dbname=' . $_POST['dbname'], $_POST['dbuser'], $_POST['dbpass']);
        $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $error = "<p class='text-center'><font color='#00cc00'><b>DB Connected:</b></font></p>";
    } catch (PDOException $e) {
        $error = "<p class='text-center'><font color='#ff0000'><b>Failed to connect to database:</b></font></p> (%d) %s<br />" . $e->getMessage();
    }
    
    $arr = ["addedrequests","attachments","bans","bookmarks","bonus","blocks","categories","censor","clients","comments",
    "countries","faq","files","forumcats","forum_posts","forum_forums","forum_readposts"
    ,"forum_topics","friends","groups","guests","languages","likes","log","messages","news"
    ,"peers","pollanswers","polls","ratings","reports","requests","rules","snatched","shoutbox","staffmessages"
    ,"stylesheets","tasks","teams","tmdb","torrentlang","torrents","users","warnings"];
    
    try {
        $sql = file_get_contents('install/Full_Database.sql');
        //$sql = "ALTER TABLE `torrents` ADD `aaaaaaannouncelist` longtext DEFAULT NULL"; // for v3
        $install_db = $link->prepare($sql);
        $install_db->execute();
        $error1 = "<p class='text-center'><font color='#00cc00'><b>DB Inserted:</b></font></p>";
    } catch (PDOException $e) {
        $error1 = "<p class='text-center'><font color='#ff0000'><b>Failed to insert to database:</b></font></p> (%d) %s<br />" . $e->getMessage();
        foreach ($arr as $table) :
            $sql = "DROP TABLE IF EXISTS `$table`";
            $install_db = $link->prepare($sql);
            $install_db->execute();
        endforeach;
    }
    $data['instruction'] = "Insert Database";
    $form .= "<form method='post' action='$_COOKIE[url]?step=4'>";
    $form .= "<input type='hidden' name='host' value='$_POST[host]'>";
    $form .= "<input type='hidden' name='dbname' value='$_POST[dbname]'>";
    $form .= "<input type='hidden' name='dbuser' value='$_POST[dbuser]'>";
    $form .= "<input type='hidden' name='dbpass' value='$_POST[dbpass]'>";
    $form .= "<div class='text-center'><input type=submit value='Next Step 4'></div>";
    $form .= "</form>";
    $data['form'] = "$error $error1";
    $data['step'] = $form;
} elseif ($step == 4) {
    // 4) check db install
    $data['instruction'] = "Check Database Details";
    if (!empty($_POST)) {
        $data['form'] = check_db();
    }
    $form .= "<form method='post' action='$_COOKIE[url]?step=5'>";
    $form .= "<input type='hidden' name='host' value='$_POST[host]'>";
    $form .= "<input type='hidden' name='dbname' value='$_POST[dbname]'>";
    $form .= "<input type='hidden' name='dbuser' value='$_POST[dbuser]'>";
    $form .= "<input type='hidden' name='dbpass' value='$_POST[dbpass]'>";
    $form .= "<div class='text-center'><input type=submit value='Next Step 5'></div>";
    $form .= "</form>";
    $data['step'] = $form;
} elseif ($step == 5) {
    // 5) enter config settings
    $data['instruction'] = "Set Config File";
    $form .= "<form method='post' action='$_COOKIE[url]?step=6'>";
    $form .= "<div class='row'>";
    $form .= "<div class='col'>";
    $form .= "<dl class='row mb-0'>";
    $form .= "<dt class='col-sm-5'>Host</dt><dd class='col-sm-7'><input type='text' class='form-control' name='CONFIG['DB_HOST']' value='$_POST[host]'></dd>";
    $form .= "<dt class='col-sm-5'>DB Username</dt><dd class='col-sm-7'><input type='text' class='form-control' name='CONFIG['DB_USER']' value='$_POST[dbuser]'></dd>";
    $form .= "<dt class='col-sm-5'>DB Password</dt><dd class='col-sm-7'><input type='text' class='form-control' name='CONFIG['DB_PASS']' value='$_POST[dbpass]'></dd>";
    $form .= "<dt class='col-sm-5'>DB Name</dt><dd class='col-sm-7'><input type='text' class='form-control' name='CONFIG['DB_NAME']' value='$_POST[dbname]'></dd>";
    $form .= "<dt class='col-sm-5'>Site Url</dt><dd class='col-sm-7'><input type='text' class='form-control' name='CONFIG['URLROOT']' placeholder='eg http://localhost/M-BiTsy'></dd>";
    $form .= "<dt class='col-sm-5'>Owner Username (Use this name on signup to access admin settings)</dt><dd class='col-sm-7'><input type='text' class='form-control' name='CONFIG['_OWNERS']' placeholder='eg M-jay or multi M-jay,M-jay'></dd>";
    $form .= "<dt class='col-sm-5'>Use Webp Images (use false if gd extension not present)</dt><dd class='col-sm-7'><input type='text' class='form-control' name='CONFIG['USE_WEBP']' placeholder='eg true or false'></dd>";
    $form .= "<dt class='col-sm-5'>TMDB API Key</dt><dd class='col-sm-7'><input type='text' class='form-control' name='CONFIG['_TMDBAPIKEY']' placeholder='eg g8rg5r5eg4e5geg4e5g54'></dd>";
    $form .= "<dt class='col-sm-5'>TMDB API Key On (use false if no key/account)</dt><dd class='col-sm-7'><input type='text' class='form-control' name='CONFIG['_TMDBswitch']' placeholder='eg true or false'></dd>";
    $form .= "<dt class='col-sm-5'>OMDB API Key</dt><dd class='col-sm-7'><input type='text' class='form-control' name='CONFIG['_OMDBAPIKEY']' placeholder='eg g8rg5r5eg4e5geg4e5g54'></dd>";
    $form .= "<dt class='col-sm-5'>OMDB API Key On (use false if no key/account)</dt><dd class='col-sm-7'><input type='text' class='form-control' name='CONFIG['_OMDBswitch']' placeholder='eg true or false'></dd>";
    $form .= "<div class='text-center'><input type=submit value='Next Final Step'></div></dd>";
    $form .= "</div>";
    $form .= "</div>";
    $form .= "</form>";
    $data['form'] = $form;
    $data['step'] = "";
} elseif ($step == 6) {
    // 6) write config settings
    write_config();
    unlink('install.php');
    if (is_dir('install')) {
        unlink('install/Full_Database.sql');
        rmdir('install');
    }
    $data['instruction'] = "Check Config";
    $data['form'] = 'Now Signup, config settings can be edited Manually  in app/config/config.php';
    $data['step'] = "<a href='$_COOKIE[url]'>index</a>";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Install</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<nav class="navbar bg-primary p-2">
  <p class='text-light'>M-BiTsy Install</p>
</nav><br>

<div class="container justify-content-md-center-2 p-2" style='background-color:#7fd6ff;color:blue;border:3px solid blue;'>
<p class='text-center'><?= $data['instruction'] ?></p>
<p class='text-center'><?= $data['form'] ?></p>
<p class='text-center'><?= $data['step'] ?></p>
</div><br>

<footer class="bg-primary fixed-bottom p-2">
  <p class='text-center text-light'>Support @<a href='' style='color:yellow'>Torrent Trader</a></p>
</footer>

</body>
</html>