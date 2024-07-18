<?php

use App\Models\ForumsModel;
use App\Models\PostsModel;
use App\Models\TopicsModel;

if (CONFIG['FORUMONINDEX']) {
    $forums_res = ForumsModel::getIndex();
    block_begin(Txt('FORUM'));
    if ($forums_res->rowCount() == 0) {
        echo Txt("NO fORUMS fOUND");
    } else {
        ?>
        <div class="p-4">
            <?php foreach ($forums_res as $cat) : ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="col-6"><?= htmlspecialchars($cat['name']) ?></th>
                                <th class="col-2"><?= Txt("Topics") ?></th>
                                <th class="col-2"><?= Txt("Posts") ?></th>
                                <th class="col-2"><?= Txt("Last post") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = ForumsModel::fetchAll('*', ['category='], [$cat['id']], 'ORDER BY sort, name');
                            foreach ($stmt as $row) {
                                $lastpostid = get_forum_last_post($row['id']);
                                $detail = lastpostdetails($lastpostid);
                                $postcount = PostsModel::postcount($row['id']);
                                $topiccount = TopicsModel::fetchColumn('count(id)', ['forumid='], [$row['id']]); ?>
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