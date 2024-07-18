<?php

use App\Models\RequestsModel;

 ?>
<?php block_begin($V_title) ?>
<a type='button' class='btn btn-sm ttbtn' data-bs-toggle='modal' data-bs-target='#addModal'><?= Txt('MAKE_REQUEST') ?></a>&nbsp;
<a href='<?= Url("request/" . $USER->has('id') . "") ?>'><button class='btn btn-sm ttbtn'><?= Txt('MY_REQUEST') ?></button></a>&nbsp;
<a href='<?= Url('request') ?>'><button class='btn btn-sm ttbtn'><?= Txt('ALL_REQUEST') ?></button></a>
<br><br>

<form method=post action='<?= Url('request/delete') ?>'>
    <div class='table-responsive  p-2'>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th><?= Txt('REQUESTS') ?></th>
                    <th><?= Txt('TYPE') ?></th>
                    <th><?= Txt('ADDED') ?></th>
                    <th><?= Txt('ADDED_BY') ?></th>
                    <th><?= Txt('FILLED') ?></th>
                    <th><?= Txt('FILLED_BY') ?></th>
                    <th><?= Txt('VOTES') ?></th>
                    <th><?= Txt('COMMENT') ?></th>
                    <th><?= Txt('DEL') ?></th>
                </tr>
            </thead><?php // todo (tidy up)
                    foreach ($V_requests as $arr) {
                        $userratio = $arr["downloaded"] > 0 ? number_format($arr["uploaded"] / $arr["downloaded"], 1) : "---";
                        if ($arr["privacy"] == "strong") {
                            if ($USER->has('class') >= 5) {
                                $addedby = "<b>" . coloredname($arr['userid']) . " (" . get_ratio_color($userratio) . ")</b>";
                            } else {
                                $addedby = "<b>" . coloredname($arr['userid']) . " (----)</b>";
                            }
                        } else {
                            $addedby = "<b>" . coloredname($arr['userid']) . " (" . get_ratio_color($userratio) . ")</b>";
                        }
                        $filled = $arr['filled'];
                        if ($filled) {
                            $filled = "<a href=$filled><font color=green><b>Yes</b></font></a>";
                            $filledbydata = "<b>" . coloredname($arr['filledby']) . "</b>";
                        } else {
                            $filled = "<a href=" . Url("request") . "><font color=red><b>No</b></font></a>";
                            $filledbydata = "<i>nobody</i>";
                        } ?>
                <tr>
                    <td><a href='#' data-bs-toggle='modal' data-bs-target='#detailModal'><b><?= $arr['request'] ?></b></a></td>
                    <td><?= $arr['parent_cat'] ?>: <?= $arr['cat'] ?></td>
                    <td><?= $arr['added'] ?></td>
                    <td><?= $addedby ?></td>
                    <td><?= $filled ?></td>
                    <td><?= $filledbydata ?></td>
                    <td><a href='#' data-bs-toggle='modal' data-bs-target='#voteModal'><b><?= $arr['hits'] ?></b></a></td>
                    <td><a href="<?= Url("comment/$arr[id]/req") ?>"><b><?= $arr["comments"] ?></b></td>
                    <?php if ($USER->has('id') == $arr['userid'] || $USER->has('class') > 5) : ?>
                        <td class=table_col1>
                            <input type="checkbox" name="delreq[]" value="<?= $arr['id'] ?>" />&nbsp;
                            <a href='javascript:;' class='editAttr' data-bs-toggle='modal' data-bs-target='#editModal' data-id='<?= $arr['id'] ?>' data-request='<?= $arr['request'] ?>' data-descr='<?= $arr['descr'] ?>' data-filled='<?= $arr['filled'] ?>' data-filledby='<?= $arr['filledby'] ?>'>
                                <i class='fa fa-edit' title='edit' aria-hidden='true'></i></a>
                        </td>
                    <?php else : ?>
                        <td>&nbsp;</td>
                    <?php endif; ?>
                </tr>

                <!-- Vote Modal -->
                <div class="modal fade" id="voteModal">
                    <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="text-center"><a href='<?= URLROOT ?>/request/addvote/<?= $arr['id'] ?>'><b><?= Txt('VOTE_FOR_THIS') ?> <?= Txt('REQUEST') ?></b></a></div>
                                <?php
                                $res1 = RequestsModel::joinuser($arr['id'])
                                ?>
                                <div class="row">
                                    <div class="col-3"><?= Txt('USERNAME') ?></div>
                                    <div class="col-3"><?= Txt('UPLOADED') ?></div>
                                    <div class="col-3"><?= Txt('DOWNLOADED') ?></div>
                                    <div class="col-3"><?= Txt('RATIO') ?></div>
                                    <?php foreach ($res1 as $arr1) :
                                        $userratio = $arr["downloaded"] > 0 ? number_format($arr1["uploaded"] / $arr1["downloaded"], 1) : "---";
                                        $uploaded = mksize($arr1["uploaded"]);
                                        $downloaded = mksize($arr1["downloaded"]);
                                        $enabled = $arr["enabled"] == 'no' ? "<font color = red>No</font>" : "<font color = green>Yes</font>"; ?>
                                        <div class="col-3"><b><?= coloredname($arr1['userid']) ?></b></div>
                                        <div class="col-3"><?= $uploaded ?></div>
                                        <div class="col-3"><?= $downloaded ?></div>
                                        <div class="col-3"><?= get_ratio_color($userratio) ?></div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class='btn ttbtn m-2' data-bs-dismiss="modal"><i class='fa fa-home me-2'></i><?= Txt("CLOSE") ?></button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Read Modal -->
                <div class="modal fade" id="detailModal">
                    <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="row justify-content-md-center">
                                    <div class="col-3"><B><?= Txt('REQUEST') ?>: </B></div>
                                    <div class="col-9"><?= $arr['request'] ?></div>
                                </div>
                                <div class="row justify-content-md-center">
                                    <div class="col-3"><B><?= Txt("CATEGORY"); ?>: </B></div>
                                    <div class="col-9"><?= $arr['cat'] ?></div>
                                </div>
                                <?php if ($arr["descr"]) : ?>
                                    <div class='row justify-content-md-center'>
                                        <div class='col-3'><B><?= Txt('DESCRIPTION') ?>: </B></div>
                                        <div class='col-9'><?= $arr['descr'] ?></div>
                                    </div>
                                <?php endif; ?>
                                <div class="row justify-content-md-center">
                                    <div class="col-3"><B><?= Txt('ADDED') ?>: </B></div>
                                    <div class="col-9"><?= $arr['added'] ?></div>
                                </div>
                                <div class="row justify-content-md-center">
                                    <div class="col-3"><B><?= Txt("REQUEST_BY"); ?>: </B></div>
                                    <div class="col-9"><?= coloredname($arr['userid']) ?></div>
                                </div>
                                <?php if ($num["filled"] == null) { ?>
                                    <div class='row justify-content-md-center'>
                                        <div class='col-3'><B><?= Txt('VOTE_FOR_THIS') ?>: </B></div>
                                        <div class='col-9'><a href="<?= URLROOT ?>/request/addvote/<?= $arr['id'] ?>"><b><?= Txt('VOTE') ?></b></a></div>
                                    </div>
                                    <div class='row justify-content-md-center'>
                                        <div class='col-3'><B><?= Txt('TO_FILL_REQUEST') ?>:</B> </div>
                                        <div class='col-9'>Enter the <b>full</b> direct URL of the torrent i.e. http://infamoustracker.org/torrents-details.php?id=134 (just copy/paste from another window/tab) or modify the existing URL to have the correct ID number</div>
                                    </div>
                                    <form method=get action="<?= URLROOT ?>/request/reqfilled/<?= $data['id'] ?>">
                                        <div class='text-center'>
                                            <input type=text size=80 name=filledurl value=TYPE-DIRECT-URL-HERE>
                                            <button class='btn btn-sm ttbtn'><?= Txt('FILL_REQUEST') ?></button>
                                        </div>
                                    </form>
                                <?php } else { ?>
                                    <div class='row justify-content-md-center'>
                                        <div class='col-2'><B><?= Txt('URL') ?>: </B></div>
                                        <div class='col-4'><a href=<?= $arr['filled'] ?> target=_new><?= $arr['filled'] ?></a></div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class='btn ttbtn m-2' data-bs-dismiss="modal"><i class='fa fa-home me-2'></i><?= Txt("CLOSE") ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </table>
    </div>
    <p class=text-center><input type=submit class='btn btn-sm ttbtn' value="<?= Txt('DEL') ?>"></p>
</form>
<?= $V_pager ?>

<!-- Add Modal -->
<form name="form" action="<?= Url('request/confirmreq') ?>" method="post"><br>
    <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-body">
                    <p class='text-center'><b>If this is abused, it will be for VIP only!</b></p>
                    <b>* Before posting a request, please make sure to search the site first to make sure it's not already posted.</b><br>
                    <b>* 1 request per day per member. Any more than that will be deleted by a moderator.</b><br>
                    <b>* When possible, please provide a full scene release name.</b><br>
                    <div class="text-center"><b><?= Txt('MAKE_REQUEST') ?></b></a></div>
                    <div class="col"><input type=text class="form-control fullwidth" placeholder="Title" name=requesttitle></div><br>
                    <div class="col">
                        <b><?= Txt('TYPE') ?>: </b>
                        <select name="cat" class='form-select'>
                            <option value="0"><?= "(" . Txt("ALL") . " " . Txt("TYPES") . ")"; ?></option>
                            <?php
                            $catdropdown = "";
                            foreach ($V_cats as $cat) {
                                $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
                                if ($cat["id"] == $_GET["cat"]) {
                                    $catdropdown .= " selected=\"selected\"";
                                }
                                $catdropdown .= ">" . htmlspecialchars($cat["parent_cat"]) . ": " . htmlspecialchars($cat["name"]) . "</option>\n";
                            }
                            echo $catdropdown ?>
                        </select>
                    </div>
                    <div class="text-center">
                        <?= Txt('ADDITIONAL_INFO_OPTINAL') ?><br>
                        <textarea class="form-control" id="descr" name="descr" rows="7"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class='btn ttbtn m-2' data-bs-dismiss="modal"><i class='fa fa-home me-2'></i><?= Txt("CLOSE") ?></button>
                    <button type="submit" class='btn ttbtn m-2'><i class='fa fa-home me-2'></i><?= Txt("SUBMIT") ?></button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $('.editAttr').click(function() {
        var id = $(this).data('id');
        var request = $(this).data('request');
        var descr = $(this).data('descr');
        var filled = $(this).data('filled');
        var filledby = $(this).data('filledby');

        $('#id').val(id);
        $('#request').val(request);
        $('#descr').val(descr);
        $('#filled').val(filled);
        $('#filledby').val(filledby);
    });
</script>
<!-- Edit Modal -->
<form name="form" action="<?= Url('request/edit') ?>" method="post"><br>
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-body">
                    <input type='hidden' name="id" id="id">
                    <label for="cat"><?= Txt("CHANGE_CAT_ID") ?>:</label><br>
                    <input type="text" class="form-control fullwidth" name="cat" value="<?= $arr['id'] ?>" id="cat"><br>
                    <label for="request"><?= Txt("REQUEST_NAME") ?>:</label><br>
                    <input type="text" class="form-control fullwidth" name="request" value="<?= $arr['request'] ?>" id="request"><br>
                    <label for="descr"><?= Txt("DESCRIPTION") ?>:</label><br>
                    <input type="text" class="form-control fullwidth" name="descr" value="<?= $arr['descr'] ?>" id="descr"><br>
                    <label for="filled"><?= Txt("URL_2_TORRENT") ?>:</label><br>
                    <input type="text" class="form-control fullwidth" name="filled" value="<?= $arr['filled'] ?>" id="filled"><br>
                    <label for="filledby"><?= Txt("FILLED_BY_USERID") ?>:</label><br>
                    <input type="text" class="form-control fullwidth" name="filledby" value="<?= $arr['filledby'] ?>" id="filledby"><br>
                </div>
                <div class="modal-footer">
                    <button type="button" class='btn ttbtn m-2' data-bs-dismiss="modal"><i class='fa fa-home me-2'></i><?= Txt("CLOSE") ?></button>
                    <button type="submit" class='btn ttbtn m-2'><i class='fa fa-home me-2'></i><?= Txt("SUBMIT") ?></button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php block_end() ?>