<?php

use App\Libs\DB;

// Get Group Name
function get_user_class_name($i)
{
    $res = DB::fetchRow('groups', 'level', ['group_id ='], [$i]);
    $row = $res;
    if ($i == $_SESSION['class']) {
        return  $row['level'];
    }
    return $row['level'];
}

// Get Poll Data
function pollresults($id)
{
    $array = array();
    $poll = DB::run("SELECT pollanswers.pollid, pollanswers.userid, pollanswers.selection FROM pollanswers  
    LEFT OUTER JOIN polls 
    ON pollanswers.pollid=polls.id
    WHERE polls.id=?
    ORDER BY pollid DESC",
    [$id])->fetchAll();
    $i = 0;
    foreach ($poll as $res) {
        $option = "option" . $res["selection"];
        if ($res["selection"] < 255) {
            $vote = DB::fetchRow('polls', $option, ['id ='], [$res['pollid']]);
        } else {
            $vote['option255'] = 'Blank vote';
        }
        $sond = DB::fetchRow('polls', 'question', ['id ='], [$res['pollid']]);
        $array[$i] = ['userid' => $res['userid'], 'question' => $sond['question'], 'vote' => $vote["$option"]];
        $i++;
    }
    return $array;
}

// Active Link
function activelink($page, $message = '')
{
    if ($message == $page && isset($_GET['type']) == $page) {
        $active = 'ttbtnactive';
        return $active;
    }
    $active = $_GET['url'] == $page ? 'ttbtnactive' : 'btn btn-sm ttbtn'; // active todo
    return $active;
}

// Profile Navbar
function usermenu($id, $page = false)
{
    $messageactive = $page == 'messages' ? 'ttbtnactive' : 'btn btn-sm ttbtn';
    ?>
    <p>
        <a href='<?php echo CONFIG['URLROOT']; ?>/profile/<?= $id ?>'><button type="button" class="<?= activelink('profile') ?>"><?= LANG["PROFILE"] ?></button></a>
        <?php if ($_SESSION['id'] == $id) : ?>
            <a href='<?= CONFIG['URLROOT'] ?>/message/mailbox'><button type="button" class="<?= $messageactive ?>"><?= LANG['MESSAGES'] ?></button></a>
        <?php else : ?>
            <a href="<?= Url("message/create/$id") ?>"><button type="button" class="<?= activelink("message/create/$id") ?>"><?= LANG["PM"] ?></button></a>
        <?php endif; ?>
        <?php if ($_SESSION['view_users'] != "yes" && $_SESSION['id'] != $id) { ?>
            <a href='<?= CONFIG['URLROOT'] ?>/friend/<?= $id ?>'><button type="button" class="<?= activelink("friend/$id") ?>"><?= LANG['FRIENDS'] ?></button></a>
        <?php } ?>

        <?php if ($_SESSION['id'] == $id) { ?>
            <a href='<?= CONFIG['URLROOT'] ?>/bookmark/<?= $id ?>'><button type="button" class="<?= activelink('bookmark') ?>"><?= LANG["BOOKMARKS"] ?></button></a>
        <?php } ?>

        <?php if ($_SESSION['view_torrents'] != "yes" && CONFIG['MEMBERSONLY']) { ?>
            <a href='<?= CONFIG['URLROOT'] ?>/peer/seeding/<?= $id ?>'><button type="button" class="<?= activelink("peer/seeding/$id") ?>"><?= LANG["SEEDING"] ?></button></a>
            <a href='<?= CONFIG['URLROOT'] ?>/peer/leeching/<?= $id ?>'><button type="button" class="<?= activelink("peer/leeching/$id") ?>"><?= LANG["DOWNLOADING"] ?></button></a>
        <?php } ?>
        <span style="float:right;">
        <?php $profile = $_GET['url']; ?>
        <?php if ($profile == "profile/$id") { ?>
                <a href="#" data-bs-toggle="modal" data-bs-target="#reportModal"><button type="button" class="<?= activelink('report') ?>"><?= LANG['REPORT'] ?></button></a>
                <a href='<?= CONFIG['URLROOT'] ?>/warning/<?= $id ?>'><button type="button" class="<?= activelink('warning') ?>"><?= LANG['WARN'] ?></button></a>
        <?php } ?>
        </span>
    </p>
    <?php
}

// Torrent Navbar
function torrentmenu($id, $external = 'no')
{
     ?>
    <a href='<?= CONFIG['URLROOT'] ?>/torrent/<?= $id ?>'><button type="button" class="<?= activelink('torrent') ?>"><?= LANG["BACK"] ?></button></a>

    <?php if ($external != 'yes') { ?>
        <a href='<?= CONFIG['URLROOT'] ?>/peer/torrent/<?= $id ?>'><button type="button" class="<?= activelink("peer/torrent/$id") ?>"><?= LANG["PEERS"] ?></button></a>
    <?php }
    ?>
    <br><br>
    <?php
}

// Torrent Navbar
function messagemenu()
{
    ?>
    <center>
        <a href="<?= CONFIG['URLROOT'] ?>/message/mailbox/inbox"><button type="button" class="<?= activelink('inbox', 'inbox') ?>"><?= LANG["INBOX"] ?></button></a>&nbsp;
        <a href="<?= CONFIG['URLROOT'] ?>/message/mailbox/outbox"><button type="button" class="<?= activelink('outbox', 'outbox') ?>"><?= LANG["OUTBOX"] ?></button></a>&nbsp;
        <a href="<?= CONFIG['URLROOT'] ?>/message/mailbox/draft"><button type="button" class="<?= activelink('draft', 'draft') ?>"><?= LANG["DRAFT"] ?></button></a>&nbsp;
        <a href="<?= CONFIG['URLROOT'] ?>/message/mailbox/templates"><button type="button" class="<?= activelink('templates', 'templates') ?>"><?= LANG["TEMPLATES"] ?></button></a>&nbsp;
        <a href="<?= CONFIG['URLROOT'] ?>/message/create"><button type="button" class="<?= activelink('messages/create') ?>"><?= LANG["COMPOSE"] ?></button></a>
    </center><br>
    <?php
}

// Sort Message Array By Type
function msgdetails($type, $arr = [])
{
    // Whos Sender
    if ($arr["sender"] == $_SESSION['id']) {
        $sender = "Yourself";
    } elseif (Id($arr["sender"])) {
        $sender = "<a href=\"/profile/$arr[sender]\">" . ($arr["sender"] ? coloredname($arr["sender"]) : "[Deleted]") . "</a>";
    } else {
        $sender = LANG["SYSTEM"];
    }

    // Whos Reciever
    if ($arr["receiver"] == $_SESSION['id']) {
        $receiver = "Yourself";
    } elseif (Id($arr["receiver"])) {
        $receiver = "<a href=\"" . CONFIG['URLROOT'] . "/profile/$arr[receiver]\">" . ($arr["receiver"] ? coloredname($arr["receiver"]) : "[" . LANG['DEL'] . "]") . "</a>";
    } else {
        $receiver = LANG["SYSTEM"];
    }

    // Subject
    $subject = "<a href='" . CONFIG['URLROOT'] . "/message/$arr[id]/$type'><b>" . format_comment($arr["subject"]) . "</b></a>";
    $added = utc_to_tz($arr["added"]);

    // Unread
    if ($arr["unread"] == "yes") {
        $unread = "<i class='fa fa-file-text tticon-red' title='" . LANG['UNREAD'] . "'></i>";
    } else {
        $unread = "<i class='fa fa-file-text' title='" . LANG['READ'] . "'></i>";
    }

    // Return In Array
    $arr = [
        'sender' => $sender,
        'receiver' => $receiver,
        'subject' => $subject,
        'unread' => $unread,
        'added' => $added
    ];
    return $arr;
}

// Get Comment Details
function userpostdetails($row, $type = null)
{
    $postername = coloredname($row["id"]);
    if ($postername == "System") {
        $title = "";
        $avatar = "";
        $usersignature = "";
        $userdownloaded = "";
        $useruploaded = "";
    } else {
        $privacylevel = $row["privacy"];
        $avatar = htmlspecialchars($row["avatar"]);
        $title = format_comment($row["title"]);
        $usersignature = stripslashes(format_comment($row["signature"]));
        $userdownloaded = mksize($row["downloaded"]);
        $useruploaded = mksize($row["uploaded"]);
    }
    $userratio = $row["downloaded"] > 0 ? number_format($row["uploaded"] / $row["downloaded"], 1) : "---";

    $commenttext = $row["body"] != '' ? format_comment($row["body"]) : format_comment($row["text"]);

    if (!$avatar) {
        $avatar = CONFIG['URLROOT'] . "/assets/images/misc/default_avatar.webp";
    }

    $edit = null;
    if ($_SESSION['edit_torrents'] == 'yes' || $_SESSION['edit_news'] == 'yes' || $_SESSION['id'] == $row['user']) {
        $edit = '<a href="javascript:;" class="editAttr" data-bs-toggle="modal" data-bs-target="#editModal" data-body="' . $commenttext . '" data-postid="' . $row['id'] . '"><i class="fa fa-edit"></i></a>';
    }

    $delete = null;
    if ($_SESSION['delete_torrents'] == 'yes' || $_SESSION['delete_news'] == 'yes') {
        $delete = '<a href="' . CONFIG['URLROOT'] . '/post/delete/' . $row['id'] . '"><i class="fa fa-trash" title="' . LANG['DEL'] . '" aria-hidden="true"></i></a>&nbsp;';
    }

    $editcomment = null;
    if ($type == "torrent" && $_SESSION['edit_torrents'] == 'yes' || $type == "news"  || $type == "req" && $_SESSION['edit_news'] == 'yes' || $_SESSION['id'] == $row['user']) {
        $editcomment = '<a href="javascript:;" class="addAttr" data-bs-toggle="modal" data-bs-target="#addModal" 
        data-id=' . $row['id'] . '
        data-text=' . $row['text'] . ' >
        <i class="fa fa-edit"></i>
        </a>&nbsp;';
    }

    $deletecomment = null;
    if ($type == "torrent" && $_SESSION['delete_torrents'] == 'yes' || $type == "req" || $type == "news" && $_SESSION['delete_news'] == 'yes') {
        $deletecomment = '<a href="' . CONFIG['URLROOT'] . '/comment/delete/' . $row["id"] . '/' . $type . '"><i class="fa fa-trash" title="' . LANG['DEL'] . '" aria-hidden="true"></i></a>&nbsp;';
    }

    $data = [
        'postername' => $postername,
        'title' => $title,
        'avatar' => $avatar,
        'usersignature' => $usersignature,
        'userdownloaded' => $userdownloaded,
        'useruploaded' => $useruploaded,
        'privacylevel' => $privacylevel,
        'userratio' => $userratio,
        'commenttext' => $commenttext,
        'edit' => $edit,
        'delete' => $delete,
        'editcomment' => $editcomment,
        'deletecomment' => $deletecomment
    ];
    return $data;
}

// Add Letter Search
function letters()
{
    print("<p class=text-center>");
    // Print Each Letter
    for ($i = 97; $i < 123; ++$i) {
        $l = chr($i);
        $L = chr($i - 32);
        $get_letter = isset($_GET['letter']) ? $_GET['letter'] : '';
        if ($l == $get_letter) {
            print("<b><font color=#ff0000>$L</font></b>\n");
        } else {
            print("<a href=?letter=$l><b>$L</b></a>\n");
        }
    }
    print("</p>");
}

// Display Username
function coloredname($id, $array = null)
{
    $classy = DB::run(
        "SELECT u.class, u.id, u.username, u.last_access, u.country, u.age, u.gender, 
        u.avatar,  u.donated, u.warned, u.enabled, 
        g.Color, g.level, u.uploaded, u.downloaded 
        FROM `users` `u` 
        INNER JOIN `groups` `g` 
        ON g.group_id=u.class 
        WHERE id ='" . $id . "'"
    )->fetch();

    if ($classy) {
        $gcolor = $classy['Color'];
        if ($classy['donated'] > 0) {
            $star = "<i class='fa fa-star' aria-hidden='true' style='color:orange' title='" . LANG['DONATED'] . "'></i>";
        } else {
            $star = "";
        }
        if ($classy['warned'] == "yes") {
            $warn = "<img src='" . CONFIG['URLROOT'] . "/assets/images/misc/warn.webp' alt='" . LANG['WARN'] . "' border='0'>";
        } else {
            $warn = "";
        }
        if ($classy['enabled'] == "no") {
            $disabled = "<img src='" . CONFIG['URLROOT'] . "/assets/images/misc/disabled.webp' title='" . LANG['DISABLED'] . "' border='0'>";
        } else {
            $disabled = "";
        }

        $name = htmlspecialchars($classy["username"]);

        $avatar = htmlspecialchars($classy["avatar"]);
        if (!$avatar) {
            $avatar = CONFIG['URLROOT'] . "/assets/images/misc/default_avatar.webp";
        }

        //if ($array != null && $_SESSION['view_users'] != "yes" && $_SESSION['id'] != $id) {
        //    return stripslashes("<a href='" . CONFIG['URLROOT'] . "/profile/" . $classy['id'] . "'  onMouseover=\"return overlib('<table class=ballooncolor border=1 width=300px align=center><tr valign=top><td class=ballooncolor align=center><img border=0 height=200 width=120 src=$avatar></td><td width=80%  class=ballooncolor><div align=left><b>" . LANG['CLASS') . ": </b><font color=" . $gcolor . ">" . $classy["level"] . "</font><br /><b>" . LANG['DONATED') . ": </b>" . $classy["donated"] . "<br /><b>" . LANG['AGE') . ": </b>" . $classy["age"] . "<br /><b>" . LANG['LAST_VISIT') . ": </b><font color=green>" . utc_to_tz($classy["last_access"]) . "</font><br /><b>" . LANG['DOWNLOADED') . ": </b><font color=red>" . mksize($classy["downloaded"]) . "</font><br /><b>" . LANG['UPLOADED') . ": </b>" . mksize($classy['uploaded']) . "</div></td></tr></table>', CENTER, HEIGHT, 200, WIDTH, 300)\"; onMouseout=\"return nd()\"><font color='" . $gcolor . "'>" . $name . "" . $star . "" . $warn . "" . $disabled . "</font></a>");
        //} else {
            return stripslashes("<a href='" . CONFIG['URLROOT'] . "/profile/" . $classy['id'] . "'><font color='#" . $gcolor . "'>" . $name . "" . $star . "" . $warn . "" . $disabled . "</font></a>");
        //}
    } else {
        return LANG['SYSTEM'];
    }
}

function pagination($rowsperpage, $countresult, $url)
{
    // Total Page Number 
    $pagetotal = ceil($countresult / $rowsperpage);

    // Get Page
    $currentpage = (isset($_GET['page'])) ? trim(filter_input(INPUT_GET, 'page')) : 0;
    if ($currentpage == 'last') {
        $page =  "$pagetotal";
    } elseif ($currentpage > 0) {
        $page = $currentpage;
    } else {
        $page = 0;
    }

    // Limit 
    $offset = max(0, ($page * $rowsperpage) - $rowsperpage);
    $limit = $rowsperpage;

    // Here we generates the range of the page numbers which will display adjacents.
    $adjacents = 3;
    if ($pagetotal <= (1 + ($adjacents * 2))) {
        $start = 1;
        $end   = $pagetotal;
    } else {
        if (($page - $adjacents) > 1) {
            if (($page + $adjacents) < $pagetotal) {
                $start = ($page - $adjacents);
                $end   = ($page + $adjacents);
            } else {
                $start = ($pagetotal - (1 + ($adjacents * 2)));
                $end   = $pagetotal;
            }
        } else {
            $start = 1;
            $end   = (1 + ($adjacents * 2));
        }
    }

    // Display Buttons
    $link = '';
    if ($pagetotal > 1) {
        $link .= '<ul class="pagination pagination-sm justify-content-left">';
        // Link of the first page
        $link .= "<li class='page-item " . ($page <= 1 ? 'tt_disabled' : '') . "'>";
        $link .= "<a class='page-link' href='" . $url . "page=1'><<</a>";
        $link .= '</li>';
        // Link of the previous page
        $link .= "<li class='page-item " . ($page <= 1 ? 'tt_disabled' : '') . "'>";
        $link .= "<a class='page-link' href='" . $url . "page=" . ($page > 1 ? ($page - 1) : 1) . "'><</a>";
        $link .= "</li>";
        // Links of the pages with page number
        for ($i = $start; $i <= $end; $i++) {
            $link .= "<li class='page-item " . ($i == $page ? 'active' : '') . "'>";
            $link .= "<a class='page-link' href='" . $url . "page=$i'>$i</a>";
            $link .= "</li>";
        }
        // Link of the next page
        $link .= "<li class='page-item " . ($page >= $pagetotal ? 'tt_disabled' : '') . "'>";
        $link .= "<a class='page-link' href='" . $url . "page=" . ($page < $pagetotal ? ($page + 1) : $pagetotal) . "'>></a>";
        $link .= "</li>";
        // Link of the last page
        $link .= "<li class='page-item " . ($page >= $pagetotal ? 'tt_disabled' : '') . "'>";
        $link .= "<a class='page-link' href='" . $url . "page=$pagetotal'>>>  ";
        $link .= "</a>";
        $link .= "</li>";
        $link .= '</ul>';
    }

    // Return Array
    return array($link, "LIMIT $offset, $limit");
}