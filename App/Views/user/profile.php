<?php
use App\Models\UsersModel;

 ?>
<?php block_begin($V_title) ?>
<?php usermenu($USER, $V_id) ?>

<div class="row p-4">
    <div class="col-md-2 text-center">
        <img class="embed-responsive" src="<?= $html['avatar'] ?>" width="70%" height="200px"><br><br>
        <?php if ($USER->has('id') == $V_id or $USER->has('class') > _UPLOADER) : ?>
            <a href='#' data-bs-toggle="modal" data-bs-target="#changepassModal"><?= Txt("RESET_PASSWORD") ?></a><br>
            <a href='#' data-bs-toggle="modal" data-bs-target="#changeemailModal"><?= Txt("RESET_EMAIL") ?></a>
        <?php endif; ?>
    </div>
    <div class="col-md-4">
        <b><?= Txt("PROFILE") ?></b><br>
        <?= Txt("USERNAME") ?>: <?= coloredname($V_user["id"]) ?><br />
        <?php if ($USER->has('class')  > _MODERATOR) : ?>
            <?= Txt("EMAIL") ?>: <?= $V_user["email"] ?><br />
            <?= Txt("PASSKEY") ?>: <?= $V_user["passkey"] ?><br />
            <?= Txt("IP") ?>: <?= $V_user["ip"] ?><br />
        <?php endif; ?>
        <?= Txt("USERCLASS") ?>: <?= get_user_class_name($V_user["class"]) ?><br />
        <?= Txt("THEME_NAME") ?>: <?= $V_user["stylesheet"] ?><br />
        <?= Txt("TITLE") ?>: <i><?= format_comment($V_user["title"]) ?></i><br />
        <?= Txt("JOINED") ?>: <?= htmlspecialchars(utc_to_tz($V_user["added"])) ?><br />
        <?= Txt("LAST_VISIT") ?>: <?= htmlspecialchars(utc_to_tz($V_user["last_access"])) ?><br />
        <?= Txt("LAST_SEEN") ?>: <?= htmlspecialchars($V_user["page"]); ?><br />
        <?= Txt("Rank") ?>: <?= $V_html["team"]; ?><br />
    </div>
    <div class="col-md-3">
        <b><?= Txt("INFO") ?></b><br>
        <?= Txt("AGE") ?>: <?= htmlspecialchars($V_user["age"]) ?><br />
        <?= Txt("GENDER") ?>: <?= Txt($V_user["gender"]) ?><br />
        <?= Txt("CLIENT") ?>: <?= htmlspecialchars($V_user["client"]) ?><br />
        <?= Txt("COUNTRY") ?>: <?= $V_html['country'] ?><br />
        <?= Txt("DONATED") ?> <?= CONFIG['CURRENCYSYMBOL']; ?><?= number_format($V_user["donated"], 2) ?><br />
        <?= Txt("WARNINGS") ?>: <?= htmlspecialchars($V_user["warned"]) ?><br />
        <?php if ($USER->permission('edit_users')) : ?>
            <?= Txt("ACCOUNT_PRIVACY_LVL") ?>: <b><?= Txt($V_user["privacy"]) ?></b><br />
        <?php endif ?>
        <?= Txt("SIGNATURE"); ?>:</b> <br><?php echo format_comment($V_html['usersignature']); ?><br><br>
        <?php if ($V_user["invited_by"]) : ?>
            <b><?= Txt("INVITED_BY") ?>:</b> <?= coloredname($V_user['invited_by']) ?><br />
        <?php endif ?>
        <?php echo "<b>" . Txt("INVITES") . ":</b> " . number_format($V_user["invites"]) . "<br />";
        $invitees = array_reverse(explode(" ", $V_user["invitees"]));
        $rows = array();
        foreach ($invitees as $invitee) {
            $row = UsersModel::fetchRow('id, username', ['id=', 'status='], [$invitee,'confirmed']);
            if ($row) {
                $rows[] = coloredname($row['id']) ;
            }
        }
        if ($rows) {
            echo "<b>" . Txt("INVITEES") . ":</b> " . implode(", ", $rows) . "<br />";
        } ?>
        [<a href="<?= Url("invite/invitetree/$V_user[id]") ?>"><?= Txt('INVIRE_TREE') ?></a>]<br>
    </div>
    <div class="col-md-3">
        <b><?= Txt("STATISTICS") ?></b><br>
        <?= Txt("UPLOADED") ?>: <?= mksize($V_user["uploaded"]) ?><br />
        <?= Txt("DOWNLOADED") ?>: <?= mksize($V_user["downloaded"]) ?><br />
        <?= Txt("RATIO") ?>: <?= get_ratio_color($V_html['userratio']) ?><br />
        <?= Txt("BONUS") ?>: <a href="<?= Url('bonus') ?>"><?= $V_user['seedbonus'] ?></a><br />
        <?= Txt("HNR") ?>: <?= $V_count['numhnr'] ?><br />
        <?= Txt("AVG_DAILY_DL") ?>: <?= mksize($V_user["downloaded"] / (DateDiff($V_user["added"], time()) / 86400)) ?><br />
        <?= Txt("AVG_DAILY_UL") ?>: <?= mksize($V_user["uploaded"] / (DateDiff($V_user["added"], time()) / 86400)) ?><br />
        <?= Txt("TORRENTS_POSTED") ?>: <a href="<?= Url("snatch/$V_user[id]/user") ?>">
            <?= number_format($V_count['numtorrents']); ?></a><br>
        <?= Txt("COMMENTS_POSTED") ?>: <a href="<?= Url("comment/$V_user[id]/user") ?>">
            <?= number_format($V_count['numcomments']); ?></a><br>
        <?= Txt("Forum Posts") ?>: <a href="<?= Url("post/user/$V_user[id]") ?>">
            <?= number_format($V_count['numforumposts']); ?></a><br>

        <?php
        if ($USER->has('id') != $V_user["id"]) {
            if ($V_friend['friend'] > 0) {
                print("[<a href=" . Url("friend/action/friend/$V_user[id]/1") . ">". Txt('REMOVE_FRIEND') ."</a>]");
            } elseif ($V_friend['enemy'] > 0) {
                print("&nbsp;[<a href=" . Url("friend/action/block/$V_user[id]/1") . ">". Txt('REMOVE_BLOCK') ."</a>]");
            } else {
                print("[<a href=" . Url("friend/action/friend/$V_user[id]") . ">". Txt('ADD_FRIEND') ."</a>]");
                print("&nbsp;[<a href=" . Url("friend/action/block/$V_user[id]") . ">". Txt('ADD_BLOCK') ."</a>]");
            }
        }
        ?>
    </div>
    <p>

        <span style="float:right;">
            <?php if ($USER->permission('edit_users')) : ?>
                <a href="<?= Url("snatch/$V_user[id]/user") ?>"><i class='fa fa-history fa-2x' title="<?= Txt("SNATCHLIST") ?>"></i></a>
            <?php endif; ?>
            <?php if ($USER->has('id') == $V_id or $USER->has('class') > _UPLOADER) : ?>
                <a href="#" data-bs-toggle="modal" data-bs-target="#editModal"><i class="fa fa-pencil fa-2x" aria-hidden="true" title="<?= Txt("EDIT") ?>"></i></a>
            <?php endif; ?>
            <?php if ($USER->has('class') >=  _UPLOADER) : ?>
                <a href="#" data-bs-toggle="modal" data-bs-target="#adminModal"><i class="fa fa-user-secret fa-2x" aria-hidden="true" title="<?= Txt("ADMIN") ?>"></i></a>
            <?php endif; ?>
        </span>
    </p>
</div>

<!-- Report Modal -->
<form id="submit" method="post" action="<?= Url('report') ?>">
    <div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-body">
                    <p class="text-center"><?= Txt('ARE_YOU_SURE_REPORT') ?> user</p>
                    <input type='hidden' name='id' id='id' value='<?= $V_id ?>' />
                    <input type='hidden' name='type' id='type' value='user' />
                    <p class="text-center"><?= Txt('ENTER_REASON') ?></p>
                    <input class="form-control fullwidth" type='text' name='reason' /><br>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= Txt("CLOSE") ?></button>
                    <button type="submit" class="btn btn-sm ttbtn"><?= Txt("REPORT") ?></button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Edit Model-->
<?php $acceptpms = $V_user['acceptpms'] == "yes"; ?>
<form id="submit" method="post" action="<?= Url("profile/submit/$V_id") ?>">
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:1200px;">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <b><?= Txt("ACCEPT_PMS") ?>:</b><br>
                            <input type='radio' name='acceptpms' <?php echo ($acceptpms ? " checked='checked'" : "") ?> value='yes' />
                            <b><?= Txt("FROM_ALL") ?></b>
                            <input type='radio' name='acceptpms' <?php echo ($acceptpms ? "" : " checked='checked'") ?> value='no' /><b><?= Txt("FROM_STAFF_ONLY") ?></b><br>
                            <b><?= Txt("ACCOUNT_PRIVACY_LVL") ?>:</b><br>
                            <?php echo priv("normal", "<b>" . Txt("NORMAL") . "</b>") . " " . priv("strong", "<b>" . Txt("STRONG") . "</b>") ?><br>
                            <i><?= Txt("ACCOUNT_PRIVACY_LVL_MSG") ?></i><br>
                            <b><?= Txt("EMAIL_NOTIFICATION") ?>:</b><br>
                            <input type='checkbox' name='pmnotif' <?php echo (strpos($V_user['notifs'], "[pm]") !== false ? " checked='checked'" : "") ?> value='yes' />
                            <b><?= Txt("PM_NOTIFY_ME") ?></b><br>
                            <b><?= Txt("THEME") ?>:</b>
                            <select name='stylesheet' class='form-select'><?= $V_dropdown['stylesheets'] ?></select>
                            <b><?= Txt("PREFERRED_CLIENT") ?>:</b><br>
                            <input type='text' size='20' maxlength='20' name='client' value="<?= htmlspecialchars($V_user['client']) ?>"><br>
                            <b><?= Txt("AGE") ?>:</b><br>
                            <input type='text' size='3' maxlength='2' name='age' value="<?= htmlspecialchars($V_user['age'])  ?>"><br>
                            <b><?= Txt("GENDER") ?>:</b>
                            <select size='1' name='gender' class='form-select'><?= $V_dropdown['gender'] ?></select>
                            <b><?= Txt("COUNTRY") ?>:</b>
                            <select name='country' class='form-select'><?= $V_dropdown['countries'] ?></select>
                            <b><?= Txt("TEAM") ?>:</b>
                            <select name='teams' class='form-select'><?= $V_dropdown['teams'] ?></select>
                        </div>
                        <div class="col-6">
                            <b><?= Txt("AVATAR") ?>:</b>
                            <input type='text' class='form-control' name='avatar' size='50' value="<?= htmlspecialchars($V_user["avatar"]) ?>">
                            <a type='button' class='model_form ttbtn btn-sm' data-bs-toggle='modal' data-bs-target='#avatarModal'>Or upload file (90px x 90px)</a><br>
                            <b><?= Txt("TITLE") ?>:</b>
                            <input type='text' class='form-control' name='title' size='50' value="<?= strip_tags($V_user["title"]) ?>">
                            <i><?= Txt("HTML_NOT_ALLOWED") ?></i><br>
                            <b><?= Txt("SIGNATURE") ?>:</b>
                            <textarea name='signature' class='form-control' style='height: 150px;'><?= htmlspecialchars($V_user["signature"]) ?></textarea>
                            <i><?php echo sprintf(Txt("MAX_CHARS"), 150) ?>, <?= Txt("HTML_NOT_ALLOWED") ?></i><br>
                            <b><?= Txt("RESET_PASSKEY") ?>:</b><br>
                            <input type='checkbox' name='resetpasskey' value='1' />&nbsp;<i><?= Txt("RESET_PASSKEY_MSG") ?></i><br>
                            <?php if (CONFIG['SHOUTBOX']) : ?>
                                <b><?= Txt("HIDE_SHOUT") ?>:</b><br>
                                <input type='checkbox' name='hideshoutbox' value='yes' <?php echo ($V_user['hideshoutbox'] == 'yes' ? 'checked="checked"' : '') ?> />
                                <?= Txt("HIDE_SHOUT") ?><br>
                            <?php endif ?>
                            <br><b><?= Txt("TIMEZONE") ?>:</b>
                            <select class='form-select' name='tzoffset'><?= $V_dropdown['tz'] ?></select>&nbsp;&nbsp;
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= Txt("CLOSE") ?></button>
                    <button type="submit" class="btn btn-sm ttbtn"><?= Txt("Save") ?></button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Admin Model-->
<?php if ($USER->has('class') > _MODERATOR) :
    $uploaded = $V_user["uploaded"];
    $downloaded = $V_user["downloaded"];
    $enabled = $V_user["enabled"] == 'yes';
    $warned = $V_user["warned"] == 'yes';
    $forumbanned = $V_user["forumbanned"] == 'yes';
    $downloadbanned = $V_user["downloadbanned"] == 'yes';
    $shoutboxpos = $V_user["shoutboxpos"] == 'yes';
    $modcomment = htmlspecialchars($V_user["modcomment"]); ?>
    <form action="<?= Url("profile/submited/$V_id") ?>" method="post">
        <div class="modal fade" id="adminModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:1200px;">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-4">
                                <?= Txt("UPLOADED") ?>:
                                <input type='text' class='form-control' size='30' name='uploaded' value="<?= mksize($V_user["uploaded"], 9) ?>">
                                <?= Txt("DOWNLOADED") ?>:
                                <input type='text' class='form-control' size='30' name='downloaded' value="<?= mksize($V_user["downloaded"], 9) ?>">
                                <?= Txt("EMAIL") ?>:
                                <input type='text' class='form-control' size='40' name='email' value="<?= $V_user['email'] ?>">
                                <?= Txt("IP_ADDRESS") ?>:
                                <input type='text' class='form-control' size='20' name='ip' value="<?= $V_user['ip'] ?>">
                                <?= Txt("INVITES") ?>:
                                <input type='text' class='form-control' size='4' name='invites' value='<?= $V_user["invites"] ?>'>
                                <?= Txt("CLASS") ?>:
                                <select name='class' class='form-control'>
                                    <?php $maxclass = $USER->has('class') + 1; ?>
                                    <?php for ($i = 1; $i < $maxclass; ++$i) : ?>
                                        <option value='<?= $i ?>' <?php echo ($V_user["class"] == $i ? "selected='selected'" : "") ?>><?= get_user_class_name($i) ?>
                                        <?php endfor; ?>
                                </select>
                                <?= Txt("DONATED") ?>: </br>
                                <input type='text' class='form-control' size='4' name='donated' value='<?= $V_user['donated'] ?>'>
                                <?= Txt("BONUS") ?>: <br>
                                <input type='text' class='form-control' size='10' name='bonus' value='<?= $V_user['seedbonus'] ?>'>
                            </div>
                            <div class="col-4">
                                <?= Txt("ACCOUNT_STATUS") ?>: <br>
                                <input name='enabled' value='yes' type='radio' <?php echo ($enabled ? " checked='checked'" : "") ?>><?= Txt("ENABLED") ?>
                                <input name='enabled' value='no' type='radio' <?php echo (!$enabled ? " checked='checked' " : "") ?>><?= Txt("DISABLED") ?><br>
                                <?= Txt("WARNED") ?>: <br>
                                <input name='warned' value='yes' type='radio' <?php echo ($warned ? " checked='checked'" : "") ?>><?= Txt("YES") ?>
                                <input name='warned' value='no' type='radio' <?php echo (!$warned ? " checked='checked'" : "") ?>><?= Txt("NO") ?><br>
                                <?= Txt("FORUM_BANNED") ?>: <br>
                                <input name='forumbanned' value='yes' type='radio' <?php echo ($forumbanned ? " checked='checked'" : "") ?>><?= Txt("YES") ?>
                                <input name='forumbanned' value='no' type='radio' <?php echo (!$forumbanned ? " checked='checked'" : "") ?>><?= Txt("NO") ?><br>
                                <?= Txt("DOWNLOAD_BANNED") ?>: <br>
                                <input name='downloadbanned' value='yes' type='radio' <?php echo ($downloadbanned ? " checked='checked'" : "") ?>><?= Txt("YES") ?>
                                <input name='downloadbanned' value='no' type='radio' <?php echo (!$downloadbanned ? " checked='checked'" : "") ?>><?= Txt("NO") ?><br>
                                <?= Txt("SHOUTBOX_BANNED") ?>: <br>
                                <input name='shoutboxpos' value='yes' type='radio' <?php echo ($shoutboxpos ? " checked='checked'" : "") ?>><?= Txt("YES") ?>
                                <input name='shoutboxpos' value='no' type='radio' <?php echo (!$shoutboxpos ? " checked='checked'" : "") ?>><?= Txt("NO") ?><br>
                            </div>
                            <div class="col-4">
                                <?= Txt("MOD_COMMENT") ?>:
                                <textarea cols='40' class='form-control' rows='10' name='modcomment'><?= $modcomment ?></textarea><BR>
                                <?= Txt("PASSWORD") ?>:
                                <input type='password' class='form-control' name='password' value="<?= $user['password'] ?>" />
                                <?= Txt("RESET_PASSWORD") ?>:
                                <input type='checkbox' name='chgpasswd' value='yes' /><br><BR>
                                <?= Txt("PASSKEY") ?>:
                                <?= $user['passkey'] ?><br />
                                <input name='resetpasskey' value='yes' type='checkbox' />&nbsp;<?= Txt("RESET_PASSKEY") ?><br>
                                (<?= Txt("RESET_PASSKEY_MSG") ?>)
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= Txt("CLOSE") ?></button>
                        <button type="submit" class="btn btn-sm ttbtn"><?= Txt("SAVE") ?></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php endif; ?>

<!-- Avatar Model-->
<form id="submit" action=' <?= Url("account/avatar/$V_id") ?>' method='post' enctype='multipart/form-data'>
    <div class="modal fade" id="avatarModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="tt_blockcenter rounded">
                        <div class="text-center">
                            <b><?= Txt("AVATAR_UPLOAD") ?>:</b> &nbsp;<br><br>
                            <input type='file' name='upfile'><br><br>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= Txt("CLOSE") ?></button>
                    <button type="submit" class="btn btn-sm ttbtn"><?= Txt("UPLOAD") ?></button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Changepass Model-->
<?php if ($USER->has('id') == $V_id) : ?>
    <form method="post" action="<?= Url("account/changepassword/$V_id") ?>">
        <div class="modal fade" id="changepassModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
                <div class="modal-content">
                    <div class="modal-body">
                        <?= Txt("NEW_PASSWORD") ?>
                        <input id="name" type="password" class="form-control fullwidth" name="chpassword" minlength="3" maxlength="25" required autofocus><br>
                        <?= Txt("REPEAT") ?>
                        <input id="name" type="password" class="form-control fullwidth" name="passagain" minlength="3" maxlength="25" required autofocus><br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= Txt("CLOSE") ?></button>
                        <button type="submit" class="btn btn-sm ttbtn"><?= Txt("SUBMIT") ?></button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Changeemail Model-->
    <form method="post" action="<?= Url("account/changeemail/$id") ?>">
        <div class="modal fade" id="changeemailModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
                <div class="modal-content">
                    <div class="modal-body">
                        <p class="text-center"><?= Txt("RESET_EMAIL") ?></p>
                        <?= Txt("EMAIL") ?>
                        <input id="name" type="email" class="form-control fullwidth" name="email" minlength="3" maxlength="100" required autofocus><br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= Txt("CLOSE") ?></button>
                        <button type="submit" class="btn btn-sm ttbtn"><?= Txt("SUBMIT") ?></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php endif; ?>
<?php block_end() ?>