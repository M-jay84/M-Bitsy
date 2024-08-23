<?php block_begin($page_title) ?>
<?php forumheader("$V_forum_name/$V_topic_name"); ?>
<div class="d-flex flex-row-reverse">
    <a type='button' class='model_form ttbtn' data-bs-toggle='modal' data-bs-target='#postModal'><?= LANG['NEW_TOPIC'] ?></a>
</div><br>

<div class=" rounded">
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="col-1"><?= LANG['READ'] ?></th>
                    <th class="col-5"><?= LANG['TOPIC'] ?></th>
                    <th class="col-1"><?= LANG['REPLIES'] ?></th>
                    <th class="col-1"><?= LANG['VIEWS'] ?></th>
                    <th class="col-1"><?= LANG['AUTHOR'] ?></th>
                    <th class="col-3"><?= LANG['LAST_POST'] ?></th> <?php
                    if ($current_user->edit_forum == 'yes' || $current_user->delete_news == 'yes') { ?>
                        <th class="col-1"><?= LANG['MODERATE'] ?></th> <?php
                    } ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($V_topics['topics'] as $row) { ?>
                    <tr>
                        <td><?= $row['read'] ?></td>
                        <td>
                            <a href='<?= Url("post/$row[id]") ?>'>
                                <b><?php echo encodehtml(stripslashes($row["subject"])) ?></b>
                            </a>
                        </td>
                        <td><?= $row['replys'] ?></td>
                        <td><?= $row["views"] ?></td>
                        <td><?= coloredname($row["userid"]) ?></td>
                        <td><?= $row['lastuserpost'] ?></td>
                        <?php
                        if ($current_user->edit_forum == 'yes' || $current_user->delete_news == 'yes') {
                            print("<td>");
                            if ($row['locked']) {
                                print("<a href='" . Url("topic/action/$V_forumid/$row[id]/unlock") . "' title='Unlock'><i class='fa fa-unlock' style='color:red;' title='" . LANG["UNLOCK"] . "'></i></a>\n");
                            } else {
                                print("<a href='" . Url("topic/action/$V_forumid/$row[id]/lock") . "' title='Lock'><i class='fa fa-lock' title='" . LANG["LOCK"] . "'></i></a>\n");
                            }
                            print("<a href='" . Url("topic/delete/$row[id]") . "' title='Delete'><i class='fa fa-trash' style='color:red;' title='" . LANG["DEL"] . "'></i></a>\n");
                            if ($row['sticky']) {
                                print("<a href='" . Url("topic/action/$V_forumid/$row[id]/unsticky") . "' title='UnStick'><i class='fa fa-exclamation' style='color:red;' title='" . LANG["UNSTICKY"] . "'></i></a>\n");
                            } else {
                                print("<a href='" . Url("topic/action/$V_forumid/$row[id]/sticky") . "' title='Stick'><i class='fa fa-exclamation' title='" . LANG["STICKY"] . "'></i></a>\n");
                            }
                            print("</td>");
                        }
                        ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div><br>

<?= $V_topics['pager'] ?>

<div class=" rounded p-1">
    <i class='fa fa-file-text' style='color:red;'></i>&nbsp;<?= LANG["NEW_POST"] ?>&nbsp;
    <i class='fa fa-file-text'></i>&nbsp;<?= LANG["READ"] ?>&nbsp;
    <i class='fa fa-lock'></i>&nbsp;<?= LANG["LOCKED"] ?>&nbsp;
    <i class='fa fa-exclamation'></i>&nbsp;<?= LANG["STICKY"] ?>
</div>

<!-- Post Modal -->
<form name="form" action="<?= Url("topic/submit/$V_forumid") ?>" method="post" enctype='multipart/form-data'><br>
    <div class="modal fade" id="postModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-body">
                    <p class='text-center'>
                        <input type='text' class='form-control fullwidth' maxlength='200' name='subject' placeholder="<?= LANG["SUBJECT"] ?>"/>
                    </p>
                    <?php textbbcode("form", "body"); ?>
                    <div class="tt_blockcenter rounded">
                        <p class="text-center"><b><?= LANG["ADD_ATTACH"]; ?></b><br>
                            <input type="file" name="upfile[]" multiple>
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class='btn ttbtn m-2' data-bs-dismiss="modal"><i class='fa fa-home me-2'></i><?= LANG['CLOSE'] ?></button>
                    <button type="submit" class='btn ttbtn m-2'><i class='fa fa-home me-2'></i><?= LANG['SUBMIT'] ?></button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php block_end() ?>