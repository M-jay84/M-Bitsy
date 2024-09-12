<?php

use App\Libs\DB;
use App\Models\ForumsModel;
use App\Models\PostsModel;
use App\Libs\Torrents\MDBS;
use App\Models\ShoutboxModel;
use App\Models\TorrentsModel;
$shouts = ShoutboxModel::shouts(20);

if (CONFIG['HNR_ON']) {
    $count = DB::fetchColumn('snatched', 'count(hnr)', ['uid=', 'hnr='], [$current_user->id, 'yes']);
    $msg = sprintf(LANG['HIT&RUNNOTICE'], coloredname($current_user->id), $count, $current_user->id);
    if ($count > 0) {
        block_begin(LANG['HIT&RUN']);
        ?>
        <div class="text-center">
            <?= $msg ?>
        </div>
        <?php
        block_end();
    }
}

if ($current_user->loggedin && !CONFIG['MEMBERSONLY']) {
    block_begin(LANG['NEWS']);
    $res = DB::run("SELECT news.id, news.title, news.added, news.body, users.username 
    FROM news 
    LEFT JOIN users 
    ON news.userid = users.id 
    ORDER BY added 
    DESC LIMIT 10");
    if ($res->rowCount() > 0) {
        print("<div class='text-center'>");
        $news_flag = 0;
        while ($array = $res->fetch(PDO::FETCH_ASSOC)) {
            if (!$array["username"]) {
                $array["username"] = LANG['UNKNOWN_USER'];
            }
            $userid = DB::fetchRow('users', 'id', ['username='], [$array["username"]]);
            $numcomm = DB::fetchColumn('comments', 'count(id)', ['news='], [$array['id']]);
            // Show first 2 items expanded
            if ($news_flag < 2) {
                $disp = "block";
                $pic = "minus";
            } else {
                $disp = "none";
                $pic = "plus";
            } ?>
            <a data-bs-toggle='collapse' data-bs-target='#collapseExample<?php echo $array["id"] ?>' aria-expanded='false' aria-controls='collapseExample<?php echo $array["id"] ?>'>
                <i class="fa fa-info-circle" aria-hidden="true"></i>
            </a>&nbsp;&nbsp;
            <?php
            print("&nbsp;<b>" . $array['title'] . "</b></a> - <b>" . LANG["POSTED"] . ":</b> " . user_timezone_date($array['added']) . " <b>" . LANG["BY"] . ":</b>" . coloredname($userid['id']) . "");
            ?>
            <div class="collapse" id="collapseExample<?php echo $array["id"] ?>">
                <?php
                print("<div> " . format_comment($array["body"]) . " <br /></div>");
                ?>
            </div> <?php
            echo "<br>" . LANG["COMMENTS"] . " (<a href='" . Url("comment/$array[id]/news") . "'>" . number_format($numcomm) . "</a>)";
            $news_flag++;
        }
        print("</div>\n");
    } else {
        echo "<br /><b>" . LANG["NO_NEWS"] . "</b>";
    }
    block_end();
}

block_begin(LANG['SHOUTBOX']);
if (CONFIG['SHOUTBOX'] && !($current_user->hideshoutbox == 'yes')) {
    if ($current_user->id) { ?>
        <p id="shoutbox"></p>
        <form id='contactForm1' name='contactForm1' action='<?= Url('shoutbox/add') ?>' method='post'>
            <div class="row">
                <div class="col-md-12">
                    <?php echo shoutbbcode("contactForm1", "message"); ?>
                </div>
            </div>
        </form> <?php
    } else {
        echo '<p id="shoutbox"></p>';
    }
    block_end();
}

if (CONFIG['LATESTFORUMPOSTONINDEX']) {
    block_begin(LANG['LATEST_POST']);
    latestforumposts();
    block_end();
}

if (CONFIG['FORUMONINDEX']) {
    $forums_res = ForumsModel::getIndex();
    block_begin(LANG['FORUM']);
    if ($forums_res->rowCount() == 0) {
        echo LANG["NO fORUMS fOUND"];
    } else {
        ?>
        <div class="p-4">
            <?php foreach ($forums_res as $cat) : ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="col-6"><?= htmlspecialchars($cat['name']) ?></th>
                                <th class="col-2"><?= LANG["TOPICS"] ?></th>
                                <th class="col-2"><?= LANG["POSTS"] ?></th>
                                <th class="col-2"><?= LANG["LAST_POST"] ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = DB::fetchAll('forum_forums', '*', ['category='], [$cat['id']], 'ORDER BY sort, name');
                            foreach ($stmt as $row) {
                                $lastpostid = get_forum_last_post($row['id']);
                                $detail = lastpostdetails($lastpostid);
                                $postcount = PostsModel::postcount($row['id']);
                                $topiccount = DB::fetchColumn('forum_topics', 'count(id)', ['forumid='], [$row['id']]); ?>
                                <tr>
                                    <td><?php
                                        echo $detail['img']; ?>&nbsp;
                                        <a href='<?= Url("topic/$row[id]") ?>'><b><?= $row['name']; ?></b></a><br>
                                        <small>- <?php echo format_comment($row['description']); ?></small><br>
                                    </td>
                                    <td>
                                        <?php echo $topiccount; ?>
                                    </td>
                                    <td>
                                        <?php echo $postcount; ?>
                                    </td>
                                    <td>
                                        <?php echo $detail['lastpost']; ?>
                                    </td>
                                </tr> <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
    block_end();
}

if ($current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
    $stmt = TorrentsModel::getAll("WHERE visible = ? AND banned = ?", ['yes', 'no']);
    block_begin(LANG['CAROUSEL']); ?>
    <div id="recipeCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner" role="listbox"> <?php
            $i = 0;
            foreach ($stmt['stmt'] as $row) {
                if ($i == 0) {
                    $set_ = 'active';
                } else {
                    $set_ = '';
                } ?>
                <div class='carousel-item <?= $set_; ?>'>
                    <div class="col-md-1">
                        <div class='scaleA'>
                            <a href='<?= Url("torrent/$row[id]") ?>'><img src="<?php echo MDBS::getImage($row); ?>" width="100" height="250" title="<?= $row['name'] ?>"></a>
                            <div class='text-center'>
                                <font color="#00cc00"><b>S</b></font> <?= $row['seeders'] ?><font color="#FF0000"><b> L </b></font><?= $row['leechers'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php $i++;
            } ?>
        </div>
        <a class="carousel-control-prev bg-transparent w-aut" href="#recipeCarousel" role="button" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </a>
        <a class="carousel-control-next bg-transparent w-aut" href="#recipeCarousel" role="button" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </a>
    </div>
<?php block_end();
}

if ($current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
    $stmt = TorrentsModel::getAll("WHERE visible = ? AND banned = ?", ['yes', 'no']);
    block_begin(LANG['GRID']);
    ?>
    <div class="row"> <?php
        foreach ($stmt['stmt'] as $row) { ?>
            <div class="col-2">
                <a href='<?= Url("torrent/$row[id]") ?>'>

                    <img src="<?= MDBS::getImage($row) ?>" width="200" height="350">
                </a><br>
                <p class="text-center"> <?php
                    echo CutName($row['name'], 20); ?><br>
                    <font color="#00cc00"><b>S</b></font> <?= $row['seeders']; ?><font color="#FF0000"><b> L </b></font><?= $row['leechers']; ?>
                </p>
            </div> <?php
        } ?>
    </div>
    <?php
    block_end();
}

block_begin(LANG['TORRENT']);
if (CONFIG['MEMBERSONLY'] && !$current_user->id) {
    echo LANG['MEMBERS_ONLY'];  
} else {
    $res = TorrentsModel::getAll("WHERE visible = ? AND banned = ?", ['yes', 'no']); 
    if ($res['count'] > 0) {
        ?>
        <div class="text-center">
            <a href='<?= Url('browse') ?>'><?= LANG["BROWSE_TORRENTS"] ?></a> - 
            <a href='<?= Url('search') ?>'><?= LANG["SEARCH_TORRENTS"] ?></a>
        </div>
        <?php
        torrenttable($res['stmt']);
    } else {
        echo LANG['NO_TORRENT_FOUND'];
    }
    if ($current_user->id) {
        DB::update('users', ['last_browse'], ['id='], [CONFIG['TT_TIME'],$current_user->id]);
    }
}
block_end();

$stmt = DB::fetchAll('users', 
    'id,username,class,donated,warned,avatar', 
    ['enabled =', 'status =', 'privacy !=', "UNIX_TIMESTAMP(?) - UNIX_TIMESTAMP(users.last_access) <="],
    ['yes', 'confirmed', 'strong', CONFIG['TT_DATE'], 86400]
);

block_begin(LANG['VISITED_USERS']);
foreach ($stmt as $row) {
    $avatar = htmlspecialchars($row["avatar"]);
    if (!$avatar) {
        $avatar = Url('assets/images/misc/default_avatar.webp');
    } ?>
    <img src='<?= $avatar; ?>' width='15px' height='15px'>
    <a href="<?= Url("profile/$row[id]") ?>">
    <small><b><?= coloredname($row["id"], $row) ?></b></small></a>&nbsp; 
<?php
}
block_end();

if (CONFIG['DISCLAIMERON']) {
    block_begin(LANG['DISCLAIMER']);
    echo LANG["DISCLAIMERTXT"];
    block_end();
}