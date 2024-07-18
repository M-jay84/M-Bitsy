<?php

use App\Models\CommentsModel;
use App\Models\NewsModel;
use App\Models\UsersModel;

if ($data['USER']->permission('view_news')) {
    block_begin(Txt('NEWS'));
    $res = NewsModel::get();
    if ($res->rowCount() > 0) {
        print("<div class='text-center'>");
        $news_flag = 0;
        while ($array = $res->fetch(PDO::FETCH_ASSOC)) {
            if (!$array["username"]) {
                $array["username"] = Txt('UNKNOWN_USER');
            }
            $userid = UsersModel::fetchRow('id', ['username='], [$array["username"]]);
            $numcomm = CommentsModel::fetchColumn('count(id)', ['news='], [$array['id']]);
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
            print("&nbsp;<b>" . $array['title'] . "</b></a> - <b>" . Txt("POSTED") . ":</b> " . date("d-M-y", utc_to_tz_time($array['added'])) . " <b>" . Txt("BY") . ":</b>" . coloredname($userid['id']) . "");
            ?>
            <div class="collapse" id="collapseExample<?php echo $array["id"] ?>">
                <?php
                print("<div> " . format_comment($array["body"]) . " <br /></div>");
                ?>
            </div> <?php
            echo "<br>" . Txt("COMMENTS") . " (<a href='" . Url("comment/$array[id]/news") . "'>" . number_format($numcomm) . "</a>)";
            $news_flag++;
        }
        print("</div>\n");
    } else {
        echo "<br /><b>" . Txt("NO_NEWS") . "</b>";
    }
    block_end();
}