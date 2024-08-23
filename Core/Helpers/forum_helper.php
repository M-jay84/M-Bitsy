<?php

use App\Libs\DB;

// Setup Forum Header
function forumheader($location = '')
{
    echo "<div class='rounded p-2'>
    <i class='fa fa-question' title='Help'></i>&nbsp;<a href='" . CONFIG['URLROOT'] . "/faq'>" . LANG["FAQ"] . "</a>&nbsp; &nbsp;&nbsp;
    <i class='fa fa-search' title='Search'></i>&nbsp;<a href='" . CONFIG['URLROOT'] . "/forum/search'>" . LANG["SEARCH"] . "</a>&nbsp; &nbsp;
    <b>" . LANG["FORUM_CONTROL"] . "</b>
    &middot; <a href='" . CONFIG['URLROOT'] . "/forum/viewunread'>" . LANG["FORUM_NEW_POSTS"] . "</a>
    &middot; <a href='" . CONFIG['URLROOT'] . "/forum/viewunread/true'>" . LANG["FORUM_MARK_READ"] . "</a>
    </div><br />";
    print("<div class='rounded p-1 g-4'>" . LANG["YOU_ARE_IN"] . ": &nbsp;<a href='" . CONFIG['URLROOT'] . "/forum'>" . LANG["FORUM"] . "</a> <b style='vertical-align:middle'>/ $location</b></div><br>");
}

// Mark all forums as read
function catch_up()
{
    if (!$_SESSION['id']) {
        return;
    }
    $userid = $_SESSION['id'];
    $res = DB::fetchAll('forum_topics', 'forum_topics', 'id, lastpost', '');
    foreach ($res as $arr) {
        $topicid = $arr["id"];
        $postid = $arr["lastpost"];
        $r = DB::fetchRow('forum_readposts', 'id,lastpostread', ['userid =', 'topicid ='], [$userid,$topicid]);
        if ($r->rowCount() == 0) {
            DB::insert('forum_readposts', ['userid' => $userid, 'topicid' => $topicid, 'lastpostread' => $postid]);
        } else {
            if ($r["lastpostread"] < $postid) {
                DB::update('forum_readposts', ['lastpostread'], ['id='], [$postid, $r["id"]]);
            }
        }
    }
}

// Returns The ID Of A Last Post In A Forum Or Otherwise 0
function get_forum_last_post($forumid)
{
    $res = DB::fetchColumn('forum_topics', 'lastpost', ['forumid ='], [$forumid], 'ORDER BY lastpost DESC LIMIT 1');
    if ($res) {
        return $res;
    } else {
        return 0;
    }
}

// LASTEST FORUM POSTS
function latestforumposts($sub_id = 0)
{
    ?>
    <div class="table-responsive tt_block">
        <table class="table table-striped ">
            <thead>
                <tr>
                    <th class="col-6">Latest Topic Title</th>
                    <th class="col-1">Replies</th>
                    <th class="col-1">Views</th>
                    <th class="col-1">Author</th>
                    <th class="col-5">Last Post</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // HERE GOES THE QUERY TO RETRIEVE DATA FROM THE DATABASE AND WE START LOOPING ///
                $for = DB::fetchAll('forum_topics', '*', '', 'ORDER BY lastpost DESC LIMIT 8');
                if ($for->rowCount() == 0) {
                    print("<b>No Latest Topics</b>");
                }
                foreach ($for as $topicarr) {
                    // Set minclass
                    $forum = DB::fetchRow('forum_forums', 'name,minclassread,guest_read', ['id ='], [$topicarr['forumid']]);
                    if ($forum && $_SESSION['class'] >= $forum["minclassread"] || $forum["guest_read"] == "yes") {
                        
                        $posts = DB::fetchColumn('forum_posts', 'count(id)', ['topicid ='], [$topicarr["id"]]);
                        $replies = max(0, $posts - 1);                    
                        $arr = DB::fetchRow('forum_posts', 'added,userid', ['topicid ='], [$topicarr["id"]], 'ORDER BY id DESC LIMIT 1');
                        ?>
                        
                        <tr>
                            <td>
                                <b><a href='<?= CONFIG['URLROOT'] ?>/post/<?= $topicarr["id"] ?>/last/#last'><?= stripslashes(encodehtml($topicarr["subject"])) ?></a></b>
                            </td>
                            <td>
                                <?= $replies ?>
                            </td>
                            <td>
                                <?= $topicarr["views"] ?>
                            </td>
                            <td>
                                <b>
                                    <center><?= coloredname($topicarr["userid"]) ?></center>
                                </b>
                            </td>
                            <td>
                                <small><b><a href='<?= CONFIG['URLROOT'] ?>/post/<?= $topicarr["id"] ?>/last/#last'><?= stripslashes(encodehtml($topicarr["subject"])) ?></a></b>&nbsp;
                                    by&nbsp;<b><?= coloredname($arr["userid"]) ?></b></small><br><small style='white-space: nowrap'><b><?= utc_to_tz($arr["added"]) ?></b></small>
                            </td>
                        <tr>
                    <?php
                    }
                } ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Get last post info in a array return img & lastpost
function lastpostdetails($lastpostid)
{
    $post_res = DB::fetchRow('forum_posts', 'added,topicid,userid', ['id ='], [$lastpostid]);
    if ($post_res) {
        $post_arr = $post_res or showMessage(Url('forum'), "Bad forum last_post");
        $lastposterid = $post_arr["userid"];
        $lastpostdate = utc_to_tz($post_arr["added"]);
        $lasttopicid = $post_arr["topicid"];
        $lastposter = coloredname($lastposterid);
        $topic_arr = DB::fetchRow('forum_topics', 'subject', ['id ='], [$lasttopicid]);
        $lasttopic = stripslashes(htmlspecialchars($topic_arr['subject']));
        //cut last topic
        $latestleng = 10;
        $lastpost = "<small><a href='" . CONFIG['URLROOT'] . "/post/$lasttopicid/last/#last'><b>" . CutName($lasttopic, $latestleng) . "</b></a> by <a href='" . CONFIG['URLROOT'] . "/profile/$lastposterid'><b>$lastposter</b></a><br />$lastpostdate</small>";
        if ($_SESSION['id']) {
            $a = DB::fetchColumn('forum_readposts', 'lastpostread', ['userid =', 'topicid ='], [$_SESSION['id'],$lasttopicid]);
        }
        //define the images for new posts or not on index
        if ($a && $a == $lastpostid) {
            $img = "<i class='fa fa-file-text' title='Read'></i>";
        } else {
            $img = "<i class='fa fa-file-text tticon-red' title='UnRead'></i>";
        }
    } else {
        $lastpost = "<span class='small'>No Posts</span>";
        $img = "folder";
    }

    $detail = [
        'img' => $img,
        'lastpost' => $lastpost,
    ];
    return $detail;
}

// Display Mod Options
function modoptions($topicid, $subject, $forumid, $locked, $sticky)
{   
    ?>
    <div class='tt_blockcenter rounded p-2'> 
        <p class='text-center'><?= LANG["FORUMS_MOD_OPTIONS"] ?></p>
        <form method='post' action='<?= CONFIG['URLROOT'] ?>/topic/rename/<?= $topicid ?>'>
           <p class='text-center'><?= LANG["RENAME"] ?></p>
           <input class='form-control fullwidth' type='text' name='subject' value='<?= stripslashes(htmlspecialchars($subject)) ?>' />
           <p class='text-center'><input type='submit'class='btn btn-sm ttbtn' value='<?= LANG["SUBMIT"] ?>' /></p>
        </form><br>
        <form method='post' action='<?= CONFIG['URLROOT'] ?>/topic/move/<?= $topicid ?>'>
           <p class='text-center'><?= LANG["MOVE"] ?></p>
            <select name='forumid' class='form-select'>
                <?php $res = DB::fetchAll('forum_forums', 'id,name,minclasswrite', '', 'ORDER BY name'); ?>
                <?php foreach ($res as $arr) : ?>
                    <?php if ($arr["id"] != $forumid && $_SESSION['class'] >= $arr["minclasswrite"]) : ?>
                       <option value='<?= $arr["id"] ?>'><?= $arr["name"] ?></option>
                    <?php endif; ?>
               <?php endforeach; ?>
            </select>
            <p class='text-center'><input type='submit'class='btn btn-sm ttbtn' value='<?= LANG["SUBMIT"] ?>' /></p>
        </form>

    <?php
    print("<p class='text-center'>\n");
    if ($locked) {
        print(LANG["LOCKED"] . ": <a href='" . CONFIG['URLROOT'] . "/topic/action/$forumid/$topicid/unlock' title='Unlock'><i class='fa fa-unlock tticon-red' title='UnLock Topic'></i></a>\n");
    } else {
        print(LANG["LOCKED"] . ": <a href='" . CONFIG['URLROOT'] . "/topic/action/$forumid/$topicid/lock' title='Lock'><i class='fa fa-lock tticon' title='Lock Topic'></i></a>\n");
    }
    print("Delete Entire Topic: <a href='" . CONFIG['URLROOT'] . "/topic/delete/$topicid' title='Delete'><i class='fa fa-trash tticon-red' title='Delete Topic'></i></a>\n");
    if ($sticky) {
        print(LANG["STICKY"] . ": <a href='" . CONFIG['URLROOT'] . "/topic/action/$forumid/$topicid/unsticky' title='UnStick'><i class='fa fa-exclamation tticon-red' title='Unstick Topic'></i></a>\n");
    } else {
        print(LANG["STICKY"] . ": <a href='" . CONFIG['URLROOT'] . "/topic/action/$forumid/$topicid/sticky' title='Stick'><i class='fa fa-exclamation tticon' title='stick Topic'></i></a>\n");
    }
    print("</p>");
    print("</div>");
}

// Get Poster Details
function posterdetails($userid)
{
    $forumposts = DB::fetchColumn('forum_posts', 'COUNT(id)', ['userid ='], [$userid]);
    $arr2 = DB::fetchRow('users', '*', ['id ='], [$userid]);
    $postername = coloredname($userid);
    $quotename = $arr2["username"];
    if ($postername == "") {
        $by = "Deluser";
        $title = "Deleted Account";
        $privacylevel = "strong";
        $usersignature = "";
        $userdownloaded = "0";
        $useruploaded = "0";
        $avatar = "";
        $nposts = "-";
        $tposts = "-";
    } else {
        $avatar = htmlspecialchars($arr2["avatar"]);
        $userdownloaded = mksize($arr2["downloaded"]);
        $useruploaded = mksize($arr2["uploaded"]);
        $privacylevel = $arr2["privacy"];
        $usersignature = stripslashes(format_comment($arr2["signature"]));
        $userratio = $arr2["downloaded"] > 0 ? number_format($arr2["uploaded"] / $arr2["downloaded"], 1) : "---";
        if (!$arr2["country"]) {
            $usercountry = "unknown";
        } else {
            $usercountry = DB::fetchRow('countries', $arr2['country']);
        }
        $title = format_comment($arr2["title"]);
        $donated = $arr2['donated'];
        $by = "<a href='" . CONFIG['URLROOT'] . "/profile/$userid'>$postername</a>" . ($donated > 0 ? "<i class='fa fa-star' aria-hidden='true' style='color:orange' title='Donated'></i>" : "") . "";
        //hide stats, but not from staff
        if ($privacylevel == "strong" && $_SESSION['control_panel'] != "yes") {
            $useruploaded = "---";
            $userdownloaded = "---";
            $userratio = "---";
            $nposts = "-";
            $tposts = "-";
        }
    }
    if (!$avatar) {
        $avatar = CONFIG['URLROOT'] . "/assets/images/misc/default_avatar.webp";
    }

    $details = [
        'by' => $by,
        'title' => $title,
        'privacylevel' => $privacylevel,
        'usersignature' => $usersignature,
        'userdownloaded' => $userdownloaded,
        'useruploaded' => $useruploaded,
        'avatar' => $avatar,
        'nposts' => $nposts,
        'tposts' => $tposts,
        'forumposts' => $forumposts,
        'userratio' => $userratio,
        'usercountry' => $usercountry,
        'quotename' => $quotename
    ];

    return $details;
}