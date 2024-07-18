<?php

use App\Models\CountriesModel;
use App\Models\ForumsModel;
use App\Models\PostsModel;
use App\Models\ReadModel;
use App\Models\TopicsModel;
use App\Models\UsersModel;

// Setup Forum Header
function forumheader($location = '')
{
    echo "<div class='rounded p-2'>
    <i class='fa fa-question' title='Help'></i>&nbsp;<a href='" . URLROOT . "/faq'>" . Txt("FAQ") . "</a>&nbsp; &nbsp;&nbsp;
    <i class='fa fa-search' title='Search'></i>&nbsp;<a href='" . URLROOT . "/forum/search'>" . Txt("SEARCH") . "</a>&nbsp; &nbsp;
    <b>" . Txt("FORUM_CONTROL") . "</b>
    &middot; <a href='" . URLROOT . "/forum/viewunread'>" . Txt("FORUM_NEW_POSTS") . "</a>
    &middot; <a href='" . URLROOT . "/forum/viewunread/true'>" . Txt("FORUM_MARK_READ") . "</a>
    </div><br />";
    print("<div class='rounded p-1 g-4'>" . Txt("YOU_ARE_IN") . ": &nbsp;<a href='" . URLROOT . "/forum'>" . Txt("FORUM") . "</a> <b style='vertical-align:middle'>/ $location</b></div><br>");
}

// Mark all forums as read
function catch_up()
{
    if (!$_SESSION['id']) {
        return;
    }
    $userid = $_SESSION['id'];
    $res = TopicsModel::fetchAll('id, lastpost', '');
    foreach ($res as $arr) {
        $topicid = $arr["id"];
        $postid = $arr["lastpost"];
        $r = ReadModel::fetchRow('id,lastpostread', ['userid =', 'topicid ='], [$userid,$topicid]);
        if ($r->rowCount() == 0) {
            ReadModel::insert(['userid' => $userid, 'topicid' => $topicid, 'lastpostread' => $postid]);
        } else {
            if ($r["lastpostread"] < $postid) {
                ReadModel::update(['lastpostread'], ['id='], [$postid, $r["id"]]);
            }
        }
    }
}

// Returns The ID Of A Last Post In A Forum Or Otherwise 0
function get_forum_last_post($forumid)
{
    $res = TopicsModel::fetchColumn('lastpost', ['forumid ='], [$forumid], 'ORDER BY lastpost DESC LIMIT 1');
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
                $for = TopicsModel::fetchAll('*', '', 'ORDER BY lastpost DESC LIMIT 8');
                if ($for->rowCount() == 0) {
                    print("<b>No Latest Topics</b>");
                }
                foreach ($for as $topicarr) {
                    // Set minclass
                    $forum = ForumsModel::fetchRow('name,minclassread,guest_read', ['id ='], [$topicarr['forumid']]);
                    if ($forum && $_SESSION['class'] >= $forum["minclassread"] || $forum["guest_read"] == "yes") {
                        
                        $posts = PostsModel::fetchColumn('count(id)', ['topicid ='], [$topicarr["id"]]);
                        $replies = max(0, $posts - 1);                    
                        $arr = PostsModel::fetchRow('added,userid', ['topicid ='], [$topicarr["id"]], 'ORDER BY id DESC LIMIT 1');
                        ?>
                        
                        <tr>
                            <td>
                                <b><a href='<?= URLROOT ?>/post/<?= $topicarr["id"] ?>/last/#last'><?= stripslashes(encodehtml($topicarr["subject"])) ?></a></b>
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
                                <small><b><a href='<?= URLROOT ?>/post/<?= $topicarr["id"] ?>/last/#last'><?= stripslashes(encodehtml($topicarr["subject"])) ?></a></b>&nbsp;
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
    $post_res = PostsModel::fetchRow('added,topicid,userid', ['id ='], [$lastpostid]);
    if ($post_res) {
        $post_arr = $post_res or Redirect(Url('forum'), "Bad forum last_post");
        $lastposterid = $post_arr["userid"];
        $lastpostdate = utc_to_tz($post_arr["added"]);
        $lasttopicid = $post_arr["topicid"];
        $lastposter = coloredname($lastposterid);
        $topic_arr = TopicsModel::fetchRow('subject', ['id ='], [$lasttopicid]);
        $lasttopic = stripslashes(htmlspecialchars($topic_arr['subject']));
        //cut last topic
        $latestleng = 10;
        $lastpost = "<small><a href='" . URLROOT . "/post/$lasttopicid/last/#last'><b>" . CutName($lasttopic, $latestleng) . "</b></a> by <a href='" . URLROOT . "/profile/$lastposterid'><b>$lastposter</b></a><br />$lastpostdate</small>";
        if ($_SESSION['id']) {
            $a = ReadModel::fetchColumn('lastpostread', ['userid =', 'topicid ='], [$_SESSION['id'],$lasttopicid]);
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
        <p class='text-center'><?= Txt("FORUMS_MOD_OPTIONS") ?></p>
        <form method='post' action='<?= URLROOT ?>/topic/rename/<?= $topicid ?>'>
           <p class='text-center'><?= Txt("RENAME") ?></p>
           <input class='form-control fullwidth' type='text' name='subject' value='<?= stripslashes(htmlspecialchars($subject)) ?>' />
           <p class='text-center'><input type='submit'class='btn btn-sm ttbtn' value='<?= Txt("SUBMIT") ?>' /></p>
        </form><br>
        <form method='post' action='<?= URLROOT ?>/topic/move/<?= $topicid ?>'>
           <p class='text-center'><?= Txt("MOVE") ?></p>
            <select name='forumid' class='form-select'>
                <?php $res = ForumsModel::fetchAll('id,name,minclasswrite', '', 'ORDER BY name'); ?>
                <?php foreach ($res as $arr) : ?>
                    <?php if ($arr["id"] != $forumid && $_SESSION['class'] >= $arr["minclasswrite"]) : ?>
                       <option value='<?= $arr["id"] ?>'><?= $arr["name"] ?></option>
                    <?php endif; ?>
               <?php endforeach; ?>
            </select>
            <p class='text-center'><input type='submit'class='btn btn-sm ttbtn' value='<?= Txt("SUBMIT") ?>' /></p>
        </form>

    <?php
    print("<p class='text-center'>\n");
    if ($locked) {
        print(Txt("LOCKED") . ": <a href='" . URLROOT . "/topic/action/$forumid/$topicid/unlock' title='Unlock'><i class='fa fa-unlock tticon-red' title='UnLock Topic'></i></a>\n");
    } else {
        print(Txt("LOCKED") . ": <a href='" . URLROOT . "/topic/action/$forumid/$topicid/lock' title='Lock'><i class='fa fa-lock tticon' title='Lock Topic'></i></a>\n");
    }
    print("Delete Entire Topic: <a href='" . URLROOT . "/topic/delete/$topicid' title='Delete'><i class='fa fa-trash tticon-red' title='Delete Topic'></i></a>\n");
    if ($sticky) {
        print(Txt("STICKY") . ": <a href='" . URLROOT . "/topic/action/$forumid/$topicid/unsticky' title='UnStick'><i class='fa fa-exclamation tticon-red' title='Unstick Topic'></i></a>\n");
    } else {
        print(Txt("STICKY") . ": <a href='" . URLROOT . "/topic/action/$forumid/$topicid/sticky' title='Stick'><i class='fa fa-exclamation tticon' title='stick Topic'></i></a>\n");
    }
    print("</p>");
    print("</div>");
}

// Get Poster Details
function posterdetails($USER, $userid)
{
    $forumposts = PostsModel::fetchColumn('COUNT(id)', ['userid ='], [$userid]);
    $arr2 = UsersModel::fetchRow('*', ['id ='], [$userid]);
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
            $usercountry = CountriesModel::fetchRow($arr2['country']);
        }
        $title = format_comment($arr2["title"]);
        $donated = $arr2['donated'];
        $by = "<a href='" . URLROOT . "/profile/$userid'>$postername</a>" . ($donated > 0 ? "<i class='fa fa-star' aria-hidden='true' style='color:orange' title='Donated'></i>" : "") . "";
        //hide stats, but not from staff
        if ($privacylevel == "strong" && $USER->has('control_panel') != "yes") {
            $useruploaded = "---";
            $userdownloaded = "---";
            $userratio = "---";
            $nposts = "-";
            $tposts = "-";
        }
    }
    if (!$avatar) {
        $avatar = URLROOT . "/assets/images/misc/default_avatar.webp";
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