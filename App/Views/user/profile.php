<?php
use App\Libs\DB;

 ?>
<?php block_begin($page_title) ?>
<?php usermenu($V_id) ?>

<div class="row p-4">
    <div class="col-md-2 text-center">
        <img class="embed-responsive" src="<?= $html['avatar'] ?>" width="70%" height="200px"><br><br>
        <?php if ($current_user->id == $V_id or $current_user->class > CONFIG['_UPLOADER']) : ?>
            <a href='#' data-bs-toggle="modal" data-bs-target="#changepassModal"><?= LANG["RESET_PASSWORD"] ?></a><br>
            <a href='#' data-bs-toggle="modal" data-bs-target="#changeemailModal"><?= LANG["RESET_EMAIL"] ?></a>
        <?php endif; ?>
    </div>
    <div class="col-md-4">
        <b><?= LANG["PROFILE"] ?></b><br>
        <?= LANG['USERNAME'] ?>: <?= coloredname($V_user["id"]) ?><br />
        <?php if ($current_user->class  > CONFIG['_MODERATOR']) : ?>
            <?= LANG["EMAIL"] ?>: <?= $V_user["email"] ?><br />
            <?= LANG["PASSKEY"] ?>: <?= $V_user["passkey"] ?><br />
            <?= LANG["IP"] ?>: <?= $V_user["ip"] ?><br />
        <?php endif; ?>
        <?= LANG["USERCLASS"] ?>: <?= get_user_class_name($V_user["class"]) ?><br />
        <?= LANG["THEME_NAME"] ?>: <?= $V_user["stylesheet"] ?><br />
        <?= LANG["TITLE"] ?>: <i><?= format_comment($V_user["title"]) ?></i><br />
        <?= LANG["JOINED"] ?>: <?= htmlspecialchars(utc_to_tz($V_user["added"])) ?><br />
        <?= LANG["LAST_VISIT"] ?>: <?= htmlspecialchars(utc_to_tz($V_user["last_access"])) ?><br />
        <?= LANG["LAST_SEEN"] ?>: <?= htmlspecialchars($V_user["page"]); ?><br />
        <?= LANG["Rank"] ?>: <?= $V_html["team"]; ?><br />
    </div>
    <div class="col-md-3">
        <b><?= LANG["INFO"] ?></b><br>
        <?= LANG["AGE"] ?>: <?= htmlspecialchars($V_user["age"]) ?><br />
        <?= LANG["GENDER"] ?>: <?= LANG[$V_user["gender"]] ?><br />
        <?= LANG["CLIENT"] ?>: <?= htmlspecialchars($V_user["client"]) ?><br />
        <?= LANG["COUNTRY"] ?>: <?= $V_html['country'] ?><br />
        <?= LANG["DONATED"] ?> <?= CONFIG['CURRENCYSYMBOL']; ?><?= number_format($V_user["donated"], 2) ?><br />
        <?= LANG["WARNINGS"] ?>: <?= htmlspecialchars($V_user["warned"]) ?><br />
        <?php if ($current_user->edit_users == 'yes') : ?>
            <?= LANG["ACCOUNT_PRIVACY_LVL"] ?>: <b><?= LANG[$V_user["privacy"]] ?></b><br />
        <?php endif ?>
        <?= LANG["SIGNATURE"]; ?>:</b> <br><?php echo format_comment($V_html['usersignature']); ?><br><br>
        <?php if ($V_user["invited_by"]) : ?>
            <b><?= LANG["INVITED_BY"] ?>:</b> <?= coloredname($V_user['invited_by']) ?><br />
        <?php endif ?>
        <?php echo "<b>" . LANG["INVITES"] . ":</b> " . number_format($V_user["invites"]) . "<br />";
        $invitees = array_reverse(explode(" ", $V_user["invitees"]));
        $rows = array();
        foreach ($invitees as $invitee) {
            $row = DB::fetchRow('users', 'id, username', ['id=', 'status='], [$invitee,'confirmed']);
            if ($row) {
                $rows[] = coloredname($row['id']) ;
            }
        }
        if ($rows) {
            echo "<b>" . LANG["INVITEES"] . ":</b> " . implode(", ", $rows) . "<br />";
        } ?>
        [<a href="<?= Url("invite/invitetree/$V_user[id]") ?>"><?= LANG['INVIRE_TREE'] ?></a>]<br>
    </div>
    <div class="col-md-3">
        <b><?= LANG["STATISTICS"] ?></b><br>
        <?= LANG["UPLOADED"] ?>: <?= mksize($V_user["uploaded"]) ?><br />
        <?= LANG["DOWNLOADED"] ?>: <?= mksize($V_user["downloaded"]) ?><br />
        <?= LANG["RATIO"] ?>: <?= get_ratio_color($V_html['userratio']) ?><br />
        <?= LANG["BONUS"] ?>: <a href="<?= Url('bonus') ?>"><?= $V_user['seedbonus'] ?></a><br />
        <?= LANG["HNR"] ?>: <?= $V_count['numhnr'] ?><br />
        <?= LANG["AVG_DAILY_DL"] ?>: <?= mksize($V_user["downloaded"] / (DateDiff($V_user["added"], time()) / 86400)) ?><br />
        <?= LANG["AVG_DAILY_UL"] ?>: <?= mksize($V_user["uploaded"] / (DateDiff($V_user["added"], time()) / 86400)) ?><br />
        <?= LANG["TORRENTS_POSTED"] ?>: <a href="<?= Url("snatch/$V_user[id]/user") ?>">
            <?= number_format($V_count['numtorrents']); ?></a><br>
        <?= LANG["COMMENTS_POSTED"] ?>: <a href="<?= Url("comment/$V_user[id]/user") ?>">
            <?= number_format($V_count['numcomments']); ?></a><br>
        <?= LANG["Forum Posts"] ?>: <a href="<?= Url("post/user/$V_user[id]") ?>">
            <?= number_format($V_count['numforumposts']); ?></a><br>

        <?php
        if ($current_user->id != $V_user["id"]) {
            if ($V_friend['friend'] > 0) {
                print("[<a href=" . Url("friend/action/friend/$V_user[id]/1") . ">". LANG['REMOVE_FRIEND'] ."</a>]");
            } elseif ($V_friend['enemy'] > 0) {
                print("&nbsp;[<a href=" . Url("friend/action/block/$V_user[id]/1") . ">". LANG['REMOVE_BLOCK'] ."</a>]");
            } else {
                print("[<a href=" . Url("friend/action/friend/$V_user[id]") . ">". LANG['ADD_FRIEND'] ."</a>]");
                print("&nbsp;[<a href=" . Url("friend/action/block/$V_user[id]") . ">". LANG['ADD_BLOCK'] ."</a>]");
            }
        }
        ?>
    </div>
    <p>

        <span style="float:right;">
            <?php if ($current_user->edit_users == 'yes') : ?>
                <a href="<?= Url("snatch/$V_user[id]/user") ?>"><i class='fa fa-history fa-2x' title="<?= LANG["SNATCHLIST"] ?>"></i></a>
            <?php endif; ?>
            <?php if ($current_user->id == $V_id or $current_user->class > CONFIG['_UPLOADER']) : ?>
                <a href="#" data-bs-toggle="modal" data-bs-target="#editModal"><i class="fa fa-pencil fa-2x" aria-hidden="true" title="<?= LANG["EDIT"] ?>"></i></a>
            <?php endif; ?>
            <?php if ($current_user->class >=  CONFIG['_UPLOADER']) : ?>
                <a href="#" data-bs-toggle="modal" data-bs-target="#adminModal"><i class="fa fa-user-secret fa-2x" aria-hidden="true" title="<?= LANG["ADMIN"] ?>"></i></a>
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
                    <p class="text-center"><?= LANG['ARE_YOU_SURE_REPORT'] ?> user</p>
                    <input type='hidden' name='id' id='id' value='<?= $V_id ?>' />
                    <input type='hidden' name='type' id='type' value='user' />
                    <p class="text-center"><?= LANG['ENTER_REASON'] ?></p>
                    <input class="form-control fullwidth" type='text' name='reason' /><br>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                    <button type="submit" class="btn btn-sm ttbtn"><?= LANG["REPORT"] ?></button>
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
                            <b><?= LANG["ACCEPT_PMS"] ?>:</b><br>
                            <input type='radio' name='acceptpms' <?php echo ($acceptpms ? " checked='checked'" : "") ?> value='yes' />
                            <b><?= LANG["FROM_ALL"] ?></b>
                            <input type='radio' name='acceptpms' <?php echo ($acceptpms ? "" : " checked='checked'") ?> value='no' /><b><?= LANG["FROM_STAFF_ONLY"] ?></b><br>
                            <b><?= LANG["ACCOUNT_PRIVACY_LVL"] ?>:</b><br>
                            <?php echo priv("normal", "<b>" . LANG["NORMAL"] . "</b>") . " " . priv("strong", "<b>" . LANG["STRONG"] . "</b>") ?><br>
                            <i><?= LANG["ACCOUNT_PRIVACY_LVL_MSG"] ?></i><br>
                            <b><?= LANG["EMAIL_NOTIFICATION"] ?>:</b><br>
                            <input type='checkbox' name='pmnotif' <?php echo (strpos($V_user['notifs'], "[pm]") !== false ? " checked='checked'" : "") ?> value='yes' />
                            <b><?= LANG["PM_NOTIFY_ME"] ?></b><br>
                            <b><?= LANG["THEME"] ?>:</b>
                            <select name='stylesheet' class='form-select'><?= $V_dropdown['stylesheets'] ?></select>
                            <b><?= LANG["PREFERRED_CLIENT"] ?>:</b><br>
                            <input type='text' size='20' maxlength='20' name='client' value="<?= htmlspecialchars($V_user['client']) ?>"><br>
                            <b><?= LANG["AGE"] ?>:</b><br>
                            <input type='text' size='3' maxlength='2' name='age' value="<?= htmlspecialchars($V_user['age'])  ?>"><br>
                            <b><?= LANG["GENDER"] ?>:</b>
                            <select size='1' name='gender' class='form-select'><?= $V_dropdown['gender'] ?></select>
                            <b><?= LANG["COUNTRY"] ?>:</b>
                            <select name='country' class='form-select'><?= $V_dropdown['countries'] ?></select>
                            <b><?= LANG["TEAM"] ?>:</b>
                            <select name='teams' class='form-select'><?= $V_dropdown['teams'] ?></select>
                        </div>
                        <div class="col-6">
                            <b><?= LANG["AVATAR"] ?>:</b>
                            <input type='text' class='form-control' name='avatar' size='50' value="<?= htmlspecialchars($V_user["avatar"]) ?>">
                            <a type='button' class='model_form ttbtn btn-sm' data-bs-toggle='modal' data-bs-target='#avatarModal'>Or upload file (90px x 90px)</a><br>
                            <b><?= LANG["TITLE"] ?>:</b>
                            <input type='text' class='form-control' name='title' size='50' value="<?= strip_tags($V_user["title"]) ?>">
                            <i><?= LANG["HTML_NOT_ALLOWED"] ?></i><br>
                            <b><?= LANG["SIGNATURE"] ?>:</b>
                            <textarea name='signature' class='form-control' style='height: 150px;'><?= htmlspecialchars($V_user["signature"]) ?></textarea>
                            <i><?php echo sprintf(LANG["MAX_CHARS"], 150) ?>, <?= LANG["HTML_NOT_ALLOWED"] ?></i><br>
                            <b><?= LANG["RESET_PASSKEY"] ?>:</b><br>
                            <input type='checkbox' name='resetpasskey' value='1' />&nbsp;<i><?= LANG["RESET_PASSKEY_MSG"] ?></i><br>
                            <?php if (CONFIG['SHOUTBOX']) : ?>
                                <b><?= LANG["HIDE_SHOUT"] ?>:</b><br>
                                <input type='checkbox' name='hideshoutbox' value='yes' <?php echo ($V_user['hideshoutbox'] == 'yes' ? 'checked="checked"' : '') ?> />
                                <?= LANG["HIDE_SHOUT"] ?><br>
                            <?php endif ?>
                            <br><b><?= LANG["TIMEZONE"] ?>:</b>
                            <select class='form-select' name='tzoffset'><?= $V_dropdown['tz'] ?></select>&nbsp;&nbsp;
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                    <button type="submit" class="btn btn-sm ttbtn"><?= LANG["Save"] ?></button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Admin Model-->
<?php if ($current_user->class > CONFIG['_MODERATOR']) :
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
                                <?= LANG["UPLOADED"] ?>:
                                <input type='text' class='form-control' size='30' name='uploaded' value="<?= mksize($V_user["uploaded"], 9) ?>">
                                <?= LANG["DOWNLOADED"] ?>:
                                <input type='text' class='form-control' size='30' name='downloaded' value="<?= mksize($V_user["downloaded"], 9) ?>">
                                <?= LANG["EMAIL"] ?>:
                                <input type='text' class='form-control' size='40' name='email' value="<?= $V_user['email'] ?>">
                                <?= LANG["IP_ADDRESS"] ?>:
                                <input type='text' class='form-control' size='20' name='ip' value="<?= $V_user['ip'] ?>">
                                <?= LANG["INVITES"] ?>:
                                <input type='text' class='form-control' size='4' name='invites' value='<?= $V_user["invites"] ?>'>
                                <?= LANG["CLASS"] ?>:
                                <select name='class' class='form-control'>
                                    <?php $maxclass = $current_user->class + 1; ?>
                                    <?php for ($i = 1; $i < $maxclass; ++$i) : ?>
                                        <option value='<?= $i ?>' <?php echo ($V_user["class"] == $i ? "selected='selected'" : "") ?>><?= get_user_class_name($i) ?>
                                        <?php endfor; ?>
                                </select>
                                <?= LANG["DONATED"] ?>: </br>
                                <input type='text' class='form-control' size='4' name='donated' value='<?= $V_user['donated'] ?>'>
                                <?= LANG["BONUS"] ?>: <br>
                                <input type='text' class='form-control' size='10' name='bonus' value='<?= $V_user['seedbonus'] ?>'>
                            </div>
                            <div class="col-4">
                                <?= LANG["ACCOUNT_STATUS"] ?>: <br>
                                <input name='enabled' value='yes' type='radio' <?php echo ($enabled ? " checked='checked'" : "") ?>><?= LANG["ENABLED"] ?>
                                <input name='enabled' value='no' type='radio' <?php echo (!$enabled ? " checked='checked' " : "") ?>><?= LANG["DISABLED"] ?><br>
                                <?= LANG["WARNED"] ?>: <br>
                                <input name='warned' value='yes' type='radio' <?php echo ($warned ? " checked='checked'" : "") ?>><?= LANG["YES"] ?>
                                <input name='warned' value='no' type='radio' <?php echo (!$warned ? " checked='checked'" : "") ?>><?= LANG["NO"] ?><br>
                                <?= LANG["FORUM_BANNED"] ?>: <br>
                                <input name='forumbanned' value='yes' type='radio' <?php echo ($forumbanned ? " checked='checked'" : "") ?>><?= LANG["YES"] ?>
                                <input name='forumbanned' value='no' type='radio' <?php echo (!$forumbanned ? " checked='checked'" : "") ?>><?= LANG["NO"] ?><br>
                                <?= LANG["DOWNLOAD_BANNED"] ?>: <br>
                                <input name='downloadbanned' value='yes' type='radio' <?php echo ($downloadbanned ? " checked='checked'" : "") ?>><?= LANG["YES"] ?>
                                <input name='downloadbanned' value='no' type='radio' <?php echo (!$downloadbanned ? " checked='checked'" : "") ?>><?= LANG["NO"] ?><br>
                                <?= LANG["SHOUTBOX_BANNED"] ?>: <br>
                                <input name='shoutboxpos' value='yes' type='radio' <?php echo ($shoutboxpos ? " checked='checked'" : "") ?>><?= LANG["YES"] ?>
                                <input name='shoutboxpos' value='no' type='radio' <?php echo (!$shoutboxpos ? " checked='checked'" : "") ?>><?= LANG["NO"] ?><br>
                            </div>
                            <div class="col-4">
                                <?= LANG["MOD_COMMENT"] ?>:
                                <textarea cols='40' class='form-control' rows='10' name='modcomment'><?= $modcomment ?></textarea><BR>
                                <?= LANG["PASSWORD"] ?>:
                                <input type='password' class='form-control' name='password' value="<?= $user['password'] ?>" />
                                <?= LANG["RESET_PASSWORD"] ?>:
                                <input type='checkbox' name='chgpasswd' value='yes' /><br><BR>
                                <?= LANG["PASSKEY"] ?>:
                                <?= $user['passkey'] ?><br />
                                <input name='resetpasskey' value='yes' type='checkbox' />&nbsp;<?= LANG["RESET_PASSKEY"] ?><br>
                                (<?= LANG["RESET_PASSKEY_MSG"] ?>)
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                        <button type="submit" class="btn btn-sm ttbtn"><?= LANG["SAVE"] ?></button>
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
                            <b><?= LANG["AVATAR_UPLOAD"] ?>:</b> &nbsp;<br><br>
                            <input type='file' name='upfile'><br><br>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                    <button type="submit" class="btn btn-sm ttbtn"><?= LANG["UPLOAD"] ?></button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Changepass Model-->
<?php if ($current_user->id == $V_id) : ?>
    <form method="post" action="<?= Url("account/changepassword/$V_id") ?>">
        <div class="modal fade" id="changepassModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
                <div class="modal-content">
                    <div class="modal-body">
                        <?= LANG["NEW_PASSWORD"] ?>
                        <input id="name" type="password" class="form-control fullwidth" name="chpassword" minlength="3" maxlength="25" required autofocus><br>
                        <?= LANG["REPEAT"] ?>
                        <input id="name" type="password" class="form-control fullwidth" name="passagain" minlength="3" maxlength="25" required autofocus><br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                        <button type="submit" class="btn btn-sm ttbtn"><?= LANG['SUBMIT'] ?></button>
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
                        <p class="text-center"><?= LANG["RESET_EMAIL"] ?></p>
                        <?= LANG["EMAIL"] ?>
                        <input id="name" type="email" class="form-control fullwidth" name="email" minlength="3" maxlength="100" required autofocus><br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                        <button type="submit" class="btn btn-sm ttbtn"><?= LANG['SUBMIT'] ?></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php endif; ?>
<?php block_end() ?>