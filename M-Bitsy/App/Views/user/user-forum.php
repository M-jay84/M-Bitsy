<?php
block_begin($TITLE);
forumheader();
latestforumposts();

foreach ($FORUMS as $country_id => $cat_info) : ?>
    <div class="table-responsive tt_block">
    <table class="table table-striped ">
        <thead class='tt_blockhead'>
            <tr>
                <th class="col-6"><?= htmlspecialchars($cat_info['c_name']) ?></th>
                <th class="col-2"><?= LANG["TOPICS"] ?></th>
                <th class="col-2"><?= LANG["POSTS"] ?></th>
                <th class="col-2"><?= LANG["LAST"] ?></th>
            </tr>
        </thead>
        <tbody> <?php
    $cat_forums = $cat_info['c_forums'];
    foreach ($cat_forums as $forum_id => $forum_info) :
        $lastpostid = get_forum_last_post($forum_id);
        $detail = lastpostdetails($lastpostid); ?>
        <tr>
            <td>
                <?php echo $detail['img']; ?>&nbsp;
                <a href='<?= Url("topic/$forum_id") ?>'><b><?= $forum_info['f_name'] ?></b></a><br>
                <small>- <?= format_comment($forum_info['f_description']) ?></small><br>
            </td>
            <td>
                <?= $forum_info['topic_count'] ?>
            </td>
            <td>
                <?= $forum_info['post_count'] ?>
            </td>
            <td>
                <?= $detail['lastpost'] ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endforeach; ?>

<div class=" rounded p-1">
    <i class='fa fa-file-text tticon-red'></i>&nbsp;<?= LANG["NEW_POST"] ?>&nbsp;
    <i class='fa fa-file-text'></i>&nbsp;<?= LANG["READ"] ?>&nbsp;
    <i class='fa fa-lock'></i>&nbsp;<?= LANG["LOCKED"] ?>&nbsp;
    <i class='fa fa-exclamation'></i>&nbsp;<?= LANG["STICKY"] ?>
</div>
<?php block_end() ?>