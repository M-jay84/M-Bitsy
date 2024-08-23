<?php
use App\Libs\DB;

?>
<?php block_begin($page_title) ?>
<div class='table '>
    <table class='table table-striped '>
        <tr>
            <?php foreach ($V_shouts as $row) : ?>
                <?php
                $ol3 = DB::fetchRow('users', 'avatar', ['id='], [$row["userid"]]);
                $av = $ol3['avatar'];
                if (!empty($av)) {
                    $av = "<img src='" . $ol3['avatar'] . "' alt='my_avatar' width='20' height='20'>";
                } else {
                    $av = "<img src='" . CONFIG['URLROOT'] . "/assets/images/misc/default_avatar.webp' alt='my_avatar' width='20' height='20'>";
                }
                if ($row['userid'] == 0) {
                    $av = "<img src='" . CONFIG['URLROOT'] . "/assets/images/misc/default_avatar.webp' alt='default_avatar' width='20' height='20'>";
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
                if ($current_user->edit_users == 'yes') {
                    echo "&nbsp<a href='" . Url("shoutbox/delete/$row[msgid]") . "'><i class='fa fa-remove tticon' ></i></a>&nbsp";
                    echo "&nbsp<a href='" . Url("shoutbox/edit?id=$row[msgid]") . "'><i class='fa fa-edit tticon' ></i></a>&nbsp";
                }
                if (!$current_user->edit_users == 'yes' && $current_user->username == $row['user']) {
                    $ts = modify('date', $row['date'], "+1 day");
                    if ($ts > CONFIG['TT_DATE']) {
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