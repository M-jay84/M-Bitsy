<?php
use App\Models\ReadModel;
use App\Models\AttachmentsModel;
?>
<div class="tt_block rounded">
    <div class="tt_blockhead text-center">
        <?= $V_title ?>
    </div>
    <div class="p-3 post-body">
        <?php forumheader("$V_info[forum_name]/<a href='".Url("topic/$V_info[forumid]")."'>$info[name]</a>"); ?>

        <?php if (!$V_info['locked'] && $V_info['maypost']) : ?>
            <div class='text-end'><a type='button' class='model_form ttbtn' data-bs-toggle='modal' data-bs-target='#postModal'><?= Txt("REPLY") ?></a></div><br>
        <?php else : ?>
            <div call='text-end'><b><?= Txt("LOCKED") ?></b></div><br>
        <?php endif; ?>

        <div class='text-center'><b><?= $V_info['subject'] ?></b></div><br>

        <?= $V_pager ?>

        <?php $pn = 0; ?>
        <?php $pc = count($V_posts); ?>
        <?php foreach ($V_posts as $row) : ?>
            <?php
            // Hyper Link
            print("<a id='post$row[id]'></a>");
            ++$pn;
            if ($pn == $pc) {
                print("<a name='last'></a>\n");
                if ($row['id'] > $V_info['lpr'] && $USER->has('id') == true) {
                    ReadModel::update(['lastpostread'], ['userid=', 'topicid='], [$row['id'],$USER->has('id'),$V_topicid]);
                }
            }
            // Details
            $added = utc_to_tz($row["added"]) . "(" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($row["added"]))) . " ago)";
            $posterdetails = posterdetails($USER, $row['userid']);
            $body = format_comment($row["body"]);
            if ($row['editedby'] != 0) {
                $body .= "<br /><br /><small><i>Last edited by <a href='" . Url("profile/$row[editedby]") . "'>$posterdetails[posterdetails]</b></a> on " . utc_to_tz($row["editedat"]) . "</i></small><br />\n";
            }
            $quote = htmlspecialchars($row["body"]);
            ?>
            <div class='row post-message'>
                <div class="col-12">
                    <div class=" rounded p-2">
                        <div class="row">
                            <div class="col-md-2">
                                <?= $posterdetails['by']; ?>
                            </div>
                            <div class="col-md-10">
                                <?= Txt('POSTED_AT') ?> <?= $added; ?>
                            </div>

                            <div class="col-md-2 d-none d-sm-block">
                                <center><i><?= $posterdetails['title']; ?></i></center><br>
                                <center><img src="<?= $posterdetails['avatar']; ?>" alt="Avatar" class='rounded-circle scaleA' /></center><br>
                                <?= Txt('UPLOADED') ?>: <?= $posterdetails['useruploaded']; ?><br>
                                <?= Txt('DOWNLOADED') ?>: <?= $posterdetails['userdownloaded']; ?><br>
                                <?= Txt('POSTS') ?>: <a href='<?= Url("post/user/$row[userid]") ?>'><?= $posterdetails['forumposts']; ?></a><br>
                                <?= Txt('RATIO') ?>: <?= $posterdetails['userratio']; ?><br>
                                <?= Txt('LOCATION') ?>: <?= $posterdetails['usercountry']; ?><br>
                            </div>
                            <div class="col-md-10"><br>
                                <?= $body ?>
                                <?php AttachmentsModel::get_attachment($row['id']); ?>
                                <?php if (!$posterdetails['usersignature']) : ?>
                                    <br>
                                <?php else : ?>
                                    <br />
                                    <hr /><br />
                                    <div class='f-sig' align='center'><?= $posterdetails['usersignature'] ?></div>
                                <?php endif; ?>
                            </div>
                            <?php if ($USER->has('id')) : ?>
                                <div class="col-md-3 d-none d-sm-block">
                                    <a href='<?= Url("profile/$row[userid]") ?>'><i class='fa fa-user tticon' title='Profile'></i></a>
                                    <a href='<?= Url("message/create/$row[userid]") ?>'><i class='fa fa-comment tticon' title='Send PM'></i></a>
                                    <a href="javascript:;" class="reportAttr" data-bs-toggle="modal" data-bs-target="#reportModal" data-id="<?= $row['id'] ?>"><i class='fa fa-flag tticon'></i></a>
                                    <a href='javascript:scroll(0,0);'><i class='fa fa-arrow-up tticon' title='<?= Txt("FORUMS_GOTO_TOP_PAGE"); ?>'></i></a>
                                    <a href='<?= Url("post/$V_topicid/?page=$_GET[page]&#post$row[id]") ?>'><i class='fa fa-anchor tticon' title='Direct Post Link'></i></a>
                                </div>
                                <div class="col-md-9 d-none d-sm-block">
                                    <?php if ($USER->has('id') !== $row["userid"] && !$row['thanked'] || $row['user'] !== $USER->has('id')) : ?>
                                        <a href='<?= Url("like/$row[id]/thanksforum/$V_topicid") ?>'><i class='fa fa-thumbs-up tticon' title='Thanks'></i></a>&nbsp;
                                    <?php endif; ?>
                                    <?php if ($USER->has('id') == $row["userid"] || $USER->permission('delete_forum') || $USER->permission('edit_forum')) : ?>
                                        <a data-bs-toggle='modal' data-bs-target='#editModal<?= $row['id'] ?>'><i class='fa fa-edit' aria-hidden='true'></i></a>&nbsp;
                                    <?php endif; ?>
                                    <?php if ($USER->permission('delete_forum')) : ?>
                                        <a href='<?= Url("post/delete/$row[id]") ?>'><i class='fa fa-trash tticon-red' title='Delete'></i></a>&nbsp;
                                    <?php endif; ?>
                                    <?php if (!$V_info['locked'] && $V_info['maypost']) : ?>
                                        <a type='button' class='model_form ttbtn' data-bs-toggle='modal' data-bs-target='#postModal'><i class='fa fa-reply tticon' title='reply'></i></a>
                                    <?php endif; ?>
                                </div>

                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Edit Modal -->
            <form name="formv" action="<?= Url('post/edit') ?>" method="post" enctype='multipart/form-data'><br>
                <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
                        <div class="modal-content">
                            <div class="modal-body">
                                <input type='hidden' name='page' value='<?= $_GET['page']; ?>'>
                                <input type='hidden' name='postid' value='<?= $row['id'] ?>'>
                                <div class='row justify-content-md-center'>
                                    <?php
                                    textbbcode("formv", "body", $row['body']) ?>
                                    <p class="text-center"><b><?= Txt("ADD_ATTACHMENT") ?></b><br>
                                        <input type="file" name="upfile[]" multiple><br>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class='btn ttbtn m-2' data-bs-dismiss="modal"><?= Txt("CLOSE") ?></button>
                                <button type="submit" class='btn ttbtn m-2'><?= Txt("SAVE") ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        <?php endforeach; ?>

        <?php if (!$V_info['locked'] && $V_info['maypost']) : ?>
            <div align='right'><a type='button' class='model_form ttbtn' data-bs-toggle='modal' data-bs-target='#postModal'><?= Txt("REPLY") ?></a></div>
        <?php else : ?>
            <div align='right'><b><?= Txt("LOCKED") ?></b></div><br>
        <?php endif; ?>

        <?= $pager ?>

        <!-- Add Modal -->
        <form name="form" action="<?= Url("post/submit/$V_topicid") ?>" method="post" enctype='multipart/form-data'><br>
            <div class="modal fade" id="postModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
                    <div class="modal-content">
                        <div class="modal-body">
                            <?php textbbcode("form", "body"); ?>
                            <div class="tt_blockcenter rounded">
                                <p class="text-center"><b><?= Txt("ADD_ATTACHMENT") ?></b><br>
                                    <input type="file" name="upfile[]" multiple></center><br><br>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class='btn ttbtn m-2' data-bs-dismiss="modal"><?= Txt("CLOSE") ?></button>
                            <button type="submit" class='btn ttbtn m-2'></i><?= Txt("SAVE") ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <script>
            $('.reportAttr').click(function() {
                var id = $(this).data('id');
                $('#id').val(id);
            });
        </script>
        <!-- Report Modal -->
        <form id="submit" method="post" action="<?= Url('report') ?>">
            <div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
                    <div class="modal-content">
                        <div class="modal-body">
                            <p class="text-center"><?= Txt("ARE_YOU_SURE_REPORT") ?> Forum.</p>
                            <input type='hidden' name='forumid' id='forumid' value='<?= $V_topicid ?>' /> <!-- forum id -->
                            <input type='hidden' name='id' id='id' /> <!-- post id -->
                            <input type='hidden' name='type' id='type' value='forum' />
                            <input class="form-control" type='text' style="width:100%;" name='reason' placeholder="<?= Txt("REASON") ?>" /><br>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= Txt("CLOSE") ?></button>
                            <button type="submit" class="btn btn-sm ttbtn"><?= Txt("REPORT") ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php

        if ($V_info['locked']) {
            print(Txt("FORUMS_TOPIC_LOCKED") . "<br /><br />\n");
        } elseif (!$V_info['maypost']) {
            print("<i>" . Txt("FORUMS_YOU_NOT_PERM_POST_FORUM") . "</i><br /><br />\n");
        }

        // MODERATOR OPTIONS
        if ($USER->permission('delete_forum') || $USER->permission('edit_forum')) {
            modoptions($V_topicid, $V_info['subject'], $V_info['forumid'], $V_info['locked'], $V_info['sticky']);
        }
        ?>
    </div>
</div>