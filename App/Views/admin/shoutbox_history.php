<?php
use App\Models\UsersModel;

?>
<?php block_begin($V_title) ?>
<div class='table '>
    <table class='table table-striped '>
        <tr>
            <?php foreach ($V_shouts as $row) : ?>
                <?php
                $ol3 = UsersModel::fetchRow('avatar', ['id='], [$row["userid"]]);
                $av = $ol3['avatar'];
                if (!empty($av)) {
                    $av = "<img src='" . $ol3['avatar'] . "' alt='my_avatar' width='20' height='20'>";
                } else {
                    $av = "<img src='" . URLROOT . "/assets/images/misc/default_avatar.webp' alt='my_avatar' width='20' height='20'>";
                }
                if ($row['userid'] == 0) {
                    $av = "<img src='" . URLROOT . "/assets/images/misc/default_avatar.webp' alt='default_avatar' width='20' height='20'>";
                }
                ?>
        <tr>
            <td>
                <small class="pull-left time d-none d-sm-block" style="width:99px;font-size:11px"><i class="fa fa-clock-o tticon"></i>&nbsp;<?php echo date('jS M,  g:ia', utc_to_tz_time($row['date'])); ?></small>
            </td>
            <td>
                <a class="pull-left d-none d-sm-block" href="#"><?= $av ?></a>
            </td>
            <td>
                <a class="pull-left" href="<?= Url("profile/$row[userid]") ?>" target="_parent"><b><?= coloredname($row['userid']) ?>:</b></a>&nbsp;
                <?php echo nl2br(format_comment($row['message'])); ?>
                <?php
                if ($USER->permission('edit_users')) {
                    echo "&nbsp<a href='" . Url("shoutbox/delete/$row[msgid]") . "'><i class='fa fa-remove tticon' ></i></a>&nbsp";
                    echo "&nbsp<a href='" . Url("shoutbox/edit?id=$row[msgid]") . "'><i class='fa fa-edit tticon' ></i></a>&nbsp";
                }
                if (!$USER->permission('edit_users') && $USER->has('username') == $row['user']) {
                    $ts = modify('date', $row['date'], "+1 day");
                    if ($ts > TT_DATE) {
                        echo "&nbsp<a href='" . Url("shoutbox/edit?id=$row[msgid]&user=$row[userid]") . "'><i class='fa fa-edit tticon' ></i></a>&nbsp";
                    }
                }
                ?>
            </td>
            <?php endforeach; ?>
        </tr>
    </table>
</div>
<?php block_end() ?>