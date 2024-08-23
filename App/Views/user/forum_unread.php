<?php block_begin($page_title) ?>
<?php forumheader('New Topics'); ?>

<div class="p-4">
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?= LANG['READ'] ?></th>
                    <th><?= LANG['TOPIC'] ?></th>
                    <th><?= LANG['FORUM'] ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($V_forums as $row) :
                    if ($row['lastpostread'] == $row['lastpost']) {
                        continue;
                    }
                    if ($current_user->class < $row['minclassread'] && $row["guest_read"] == "no") {
                        continue;
                    } ?>
                    <tr>
                        <td valign='middle'><i class='fa fa-file-text tticon-red' title='UnRead'></td>
                        <td><a href='<?= Url("post/$row[topicid]/last/#last") ?>'><b><?= stripslashes(htmlspecialchars($row["subject"])) ?></b></a></td>
                        <td valign='left'><a href='<?= Url("topic/$row[forumid]") ?>'><b><?= $row['name'] ?></b></a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php block_end() ?>