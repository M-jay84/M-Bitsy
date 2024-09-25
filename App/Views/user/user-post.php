<?php

use App\Libs\DB;
use App\Models\ReadLogic;
use App\Models\AttachmentsLogic;
?>
<div class="tt_block rounded">
    <div class="tt_blockhead text-center">
        <?= $TITLE ?>
    </div>
    <div class="p-3 post-body">
        <?php forumheader("$INFO[forum_name]/<a href='".Url("topic/$INFO[forumid]")."'>$INFO[name]</a>"); ?>

        <?php if (!$INFO['locked'] && $INFO['maypost']) : ?>
            <div class='text-end'><a type='button' class='model_form ttbtn' data-bs-toggle='modal' data-bs-target='#postModal'><?= LANG["REPLY"] ?></a></div><br>
        <?php else : ?>
            <div call='text-end'><b><?= LANG["LOCKED"] ?></b></div><br>
        <?php endif; ?>

        <div class='text-center'><b><?= $INFO['subject'] ?></b></div><br>

        <?= $PAGER ?>

        <?php $pn = 0; ?>
        <?php $pc = count($POSTS); ?>
        <?php foreach ($POSTS as $row) : ?>
            <?php
            // Hyper Link
            print("<a id='post$row[id]'></a>");
            ++$pn;
            if ($pn == $pc) {
                print("<a name='last'></a>\n");
                if ($row['id'] > $INFO['lpr'] && $USER->id == true) {
                    $readposts_query = "UPDATE forum_readposts SET lastpostread = ? WHERE userid=? AND topicis=?";
                    DB::run($readposts_query, [$row['id'],$USER->id,$TOPICID]);
                }
            }
            // Details
            $added = user_timezone_date($row["added"]) . "(" . (get_elapsed_time(strtotime($row["added"]))) . " ago)";
            $posterdetails = posterdetails($row['userid']);
            $body = format_comment($row["body"]);
            if ($row['editedby'] != 0) {
                $body .= "<br /><br /><small><i>Last edited by <a href='" . Url("profile/$row[editedby]") . "'>$posterdetails[posterdetails]</b></a> on " . user_timezone_date($row["editedat"]) . "</i></small><br />\n";
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
                                <?= LANG['POSTED_AT'] ?> <?= $added; ?>
                            </div>

                            <div class="col-md-2 d-none d-sm-block">
                                <center><i><?= $posterdetails['title']; ?></i></center><br>
                                <center><img src="<?= $posterdetails['avatar']; ?>" alt="Avatar" class='rounded-circle scaleA' /></center><br>
                                <?= LANG['UPLOADED'] ?>: <?= $posterdetails['useruploaded']; ?><br>
                                <?= LANG['DOWNLOADED'] ?>: <?= $posterdetails['userdownloaded']; ?><br>
                                <?= LANG['POSTS'] ?>: <a href='<?= Url("post/user/$row[userid]") ?>'><?= $posterdetails['forumposts']; ?></a><br>
                                <?= LANG['RATIO'] ?>: <?= $posterdetails['userratio']; ?><br>
                            </div>
                            <div class="col-md-10"><br>
                                <?= $body ?>
                                <?php AttachmentsLogic::get_attachment($row['id']); ?>
                                <?php if (!$posterdetails['usersignature']) : ?>
                                    <br>
                                <?php else : ?>
                                    <br />
                                    <hr /><br />
                                    <div class='f-sig' align='center'><?= $posterdetails['usersignature'] ?></div>
                                <?php endif; ?>
                            </div>
                            <?php if ($USER->id) : ?>
                                <div class="col-md-3 d-none d-sm-block">
                                    <a href='<?= Url("profile/$row[userid]") ?>'><i class='fa fa-user tticon' title='Profile'></i></a>
                                    <a href='<?= Url("message/create/$row[userid]") ?>'><i class='fa fa-comment tticon' title='Send PM'></i></a>
                                    <a href="javascript:;" class="reportAttr" data-bs-toggle="modal" data-bs-target="#reportModal" data-id="<?= $row['id'] ?>"><i class='fa fa-flag tticon'></i></a>
                                    <a href='javascript:scroll(0,0);'><i class='fa fa-arrow-up tticon' title='<?= LANG["FORUMS_GOTO_TOP_PAGE"]; ?>'></i></a>
                                    <a href='<?= Url("post/$TOPICID/?page=$_GET[page]&#post$row[id]") ?>'><i class='fa fa-anchor tticon' title='Direct Post Link'></i></a>
                                </div>
                                <div class="col-md-9 d-none d-sm-block">
                                    <?php if ($USER->id !== $row["userid"] && !$row['thanked'] || $row['user'] !== $USER->id) : ?>
                                        <a href='<?= Url("like/$row[id]/thanksforum/$TOPICID") ?>'><i class='fa fa-thumbs-up tticon' title='Thanks'></i></a>&nbsp;
                                    <?php endif; ?>
                                    <?php if ($USER->id == $row["userid"] || $USER->delete_news == 'yes' || $USER->edit_forum == 'yes') : ?>
                                        <a data-bs-toggle='modal' data-bs-target='#editModal<?= $row['id'] ?>'><i class='fa fa-edit' aria-hidden='true'></i></a>&nbsp;
                                    <?php endif; ?>
                                    <?php if ($USER->delete_news == 'yes') : ?>
                                        <a href='<?= Url("post/delete/$row[id]") ?>'><i class='fa fa-trash tticon-red' title='Delete'></i></a>&nbsp;
                                    <?php endif; ?>
                                    <?php if (!$INFO['locked'] &&$INFO['maypost']) : ?>
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
                                    <p class="text-center"><b><?= LANG["ADD_ATTACHMENT"] ?></b><br>
                                        <input type="file" name="file[]" multiple><br>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class='btn ttbtn m-2' data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                                <button type="submit" class='btn ttbtn m-2'><?= LANG["SAVE"] ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        <?php endforeach; ?>

        <?php if (!$INFO['locked'] && $INFO['maypost']) : ?>
            <div align='right'><a type='button' class='model_form ttbtn' data-bs-toggle='modal' data-bs-target='#postModal'><?= LANG["REPLY"] ?></a></div>
        <?php else : ?>
            <div align='right'><b><?= LANG["LOCKED"] ?></b></div><br>
        <?php endif; ?>

        <?= $PAGER ?>

        <!-- Add Modal -->
        <form name="form" action="<?= Url("post/submit/$TOPICID") ?>" method="post" enctype='multipart/form-data'><br>
            <div class="modal fade" id="postModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
                    <div class="modal-content">
                        <div class="modal-body">
                            <?php textbbcode("form", "body"); ?>
                            <div class="tt_blockcenter rounded">
                                <p class="text-center"><b><?= LANG["ADD_ATTACHMENT"] ?></b><br>
                                    <input type="file" name="file[]" multiple></center><br><br>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class='btn ttbtn m-2' data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                            <button type="submit" class='btn ttbtn m-2'></i><?= LANG["SAVE"] ?></button>
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
                            <p class="text-center"><?= LANG["ARE_YOU_SURE_REPORT"] ?> Forum.</p>
                            <input type='hidden' name='forumid' id='forumid' value='<?= $TOPICID ?>' /> <!-- forum id -->
                            <input type='hidden' name='id' id='id' /> <!-- post id -->
                            <input type='hidden' name='type' id='type' value='forum' />
                            <input class="form-control" type='text' style="width:100%;" name='reason' placeholder="<?= LANG["REASON"] ?>" /><br>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                            <button type="submit" class="btn btn-sm ttbtn"><?= LANG["REPORT"] ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php

        if ($INFO['locked']) {
            print(LANG["FORUMS_TOPIC_LOCKED"] . "<br /><br />\n");
        } elseif (!$INFO['maypost']) {
            print("<i>" . LANG["FORUMS_YOU_NOT_PERM_POST_FORUM"] . "</i><br /><br />\n");
        }

        // MODERATOR OPTIONS
        if ($USER->delete_news == 'yes' || $cUSER->edit_forum == 'yes') {
            modoptions($TOPICID, $INFO['subject'], $INFO['forumid'], $INFO['locked'], $INFO['sticky']);
        }
        ?>
    </div>
</div>