<?php block_begin($data['title']) ?>
<p class="text-center"><a href='<?= Url("profile/$data[id]")  ?>'>Go To Profile</a></p>
<?php
foreach ($data['res'] as $row) {
    // Quick Function To Tidy Results
    $user = userpostdetails($row); ?>
    <div class="row tt_block rounded p-1">
        <div class="col-2 d-none d-sm-block"> <?php
            if (!$current_user->edit_users == 'yes' && $user['privacylevel'] == 'strong') {
                print('<center>' . coloredname($row['userid']) . '<br /><i>' . $user['title'] . '</i><img width="80" height="80" src="' . $user['avatar'] . '" alt="" /><br />Uploaded: ---<br />Downloaded: ---<br />Ratio: ' . get_ratio_color('---') . '</small><br /><br /><a href="' . Url("profile/$user[id]") . '"><i class="fa fa-user tticon" title="Profile"></i></a> <a href="' . Url("message/create/$user[id]") . '"><i class="fa fa-comment" title="Send PM"></i></a></center>');
            } else {
                print('<center>' . coloredname($row['userid']) . '<br><i>' . $user['title'] . '</i><img width="80" height="80" src="' . $user['avatar'] . '" alt="" /><br />Uploaded: ' . $user['useruploaded'] . '<br />Downloaded: ' . $user['userdownloaded'] . '<br />Ratio: ' . get_ratio_color($user['userratio']) . '</small><br /><a href="' . Url("profile/$user[id]") . '"><i class="fa fa-user" title="Profile"></i></a> <a href="'.Url("message/create/$user[id]") . '"><i class="fa fa-comment" title="Send PM"></i></a></center>');
            } ?>
        </div>
        <div class="col-10">
            <a href="<?= Url("post/$row[topicid]") ?>"><b><?= $row['subject'] ?></b></a> Posted: <?php echo date("d-m-Y \\a\\t H:i:s", utc_to_tz_time($row["added"]))  ?><a id="comment<?= $row["id"]  ?>"></a> in <a href="<?= Url("topic/$row[forumid]") ?>"><b><?= $row['name'] ?></b></a>
            <hr />
            <?= $user['commenttext']  ?>
            <hr />
            <?= $user['usersignature']  ?>
        </div>
    </div><br>
<?php
}

print($data['pager']);
block_end();