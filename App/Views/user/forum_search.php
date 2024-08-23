<?php block_begin($title) ?>
<?php forumheader('search'); ?>
<div class="tt_blockcenter p-2">
    <form method='get' action='<?= Url('forum/search') ?>'>
        <div class="row justify-content-md-center">
            <input id="keywords" type="text" class="form-control w-50" name="keywords" minlength="3" maxlength="25">
        </div>
        <div class="text-center">
            <button type='submit' class='btn btn-sm ttbtn' value='Search'><?= LANG['SEARCH_TOPIC'] ?></button>&nbsp;&nbsp;
            <button type='Submit' class='btn btn-sm ttbtn' name='type' value='deep'><?= LANG['SEARCH_POST'] ?></button>
        </div>
    </form>
</div>

<div class="table-responsive p-3">
    <p class="text-center">
        <?= LANG['SEARCH'] ?>: <b><?= htmlspecialchars($keywords) ?></b>
    </p>
    <table class="table table-striped">
        <thead>
            <tr>
                <th><?= LANG['SUBJECT'] ?></th>
                <th><?= LANG['FORUM'] ?></th>
                <th><?= LANG['ADDED'] ?></th>
                <th><?= LANG['ADDED_BY'] ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($forums as $row) {
                if ($row["name"] == "" || ($row["minclassread"] > $current_user->class && $row["guest_read"] == "no")) {
                    continue;
                } ?>
                <tr>
                    <td><a href='<?= Url("post/$row[topicid]") ?>'><?= $row['subject'] ?></a></td>
                    <td><a href='<?= Url("topic/$row[forumid]") ?>'><?= $row['name'] ?></a></td>
                    <td><?= $row['added'] ?></td>
                    <td><?= coloredname($row['userid']) ?></td>
                </tr> <?php
            } ?>
        </tbody>
    </table>
</div>
<?php $pager ?>
<?php block_end(); ?>