<?php

use App\Libs\DB;

if ($current_user->view_users != "yes" && $current_user->class <= CONFIG['_MODERATOR']) {
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
            print("&nbsp;<b>" . $array['title'] . "</b></a> - <b>" . LANG["POSTED"] . ":</b> " . date("d-M-y", utc_to_tz_time($array['added'])) . " <b>" . LANG["BY"] . ":</b>" . coloredname($userid['id']) . "");
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