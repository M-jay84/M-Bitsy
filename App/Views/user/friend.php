<?php block_begin($V_title) ?>
<?php usermenu($USER, $V_userid); ?>
<div class="text-center">
    <b><?= $V_type ?> list</b><br>
    <a href="<?= Url("friend/$V_userid/enemy") ?>"><?= Txt("ENEMY") ?></a> | 
    <a href="<?= Url("friend/$V_userid") ?>"><?= Txt("FRIEND") ?></a>
</div>
<?php foreach ($V_friends as $row) : ?>
    <?php
    $avatar = htmlspecialchars($row["avatar"]);
    if (!$avatar) {
        $avatar = Url('assets/images/misc/default_avatar.webp');
    }
    ?>
    <div class="row p-2">
        <div class="col-md-3 tt_block rounded p-4">
            <img width=80px src="<?= $avatar ?>">&nbsp;<b><?= coloredname($row['id']) ?></b> &nbsp;
            <a href='<?= Url("message/create/$row[id]") ?>'><i class="fa fa-comment" title="Send PM"></i></a>&nbsp;
            <a href='<?= Url("friend/action/friend/$row[id]/1") ?>'><i class='fa fa-trash tticon-red' title='Remove'></i></a>
            <div style='margin-top:10px; margin-bottom:2px'>Last seen: <?php echo  date("<\\b>d.M.Y<\\/\\b> H:i", utc_to_tz_time($row['last_access'])) ?></div>
            [<b><?= get_elapsed_time(sql_timestamp_to_unix_timestamp($row['last_access'])) ?> ago</b>]
        </div>
    </div>
<?php endforeach; ?>

<?php block_end(); ?>