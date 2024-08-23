<?php

use App\Models\ForumsModel;
use App\Models\PostsModel;
use App\Libs\DB;

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