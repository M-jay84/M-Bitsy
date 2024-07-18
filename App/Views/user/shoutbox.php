<?php block_begin($V_title) ?>
<div>
    <table class='table table-striped'>
        <?php foreach ($V_shouts as $row) {
            $av = isset($row['avatar']) ? $row['avatar'] : URLROOT.'/assets/images/misc/default_avatar.webp'; ?>
            <tr>
            <td style="width:25%">
                <small style="width:99px;font-size:11px"><i class="fa fa-clock-o"></i>&nbsp;<?php echo date('jS M,  g:ia', utc_to_tz_time($row['date'])); ?></small>
                <img src='<?= $av ?>' alt='my_avatar' width='20' height='20'>&nbsp;
                <b><?= coloredname($row['userid']) ?>:</b>
            </td>
            <td style="width:75%">
                <?= nl2br(format_comment($row['message'])); ?>
            </td>
            </tr>
        <?php } ?>
    </table>
</div>
<?php block_end() ?>