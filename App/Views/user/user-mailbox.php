<?php block_begin($page_title) ?>
<?php usermenu($current_user->id, 'messages'); ?>
<form id='messages' method='post' action='<?= Url("message/delete/$V_type") ?>'>
    <div class='table-responsive  rounded'>
        <?php echo messagemenu(); ?>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
                    <th><?= LANG["READ"] ?></th>
                    <th><?= LANG["SENDER"] ?></th>
                    <th><?= LANG["RECEIVER"] ?></th>
                    <th><?= LANG["SUBJECT"] ?></th>
                    <th><?= LANG["DATE"] ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($V_messages as $arr) :
                    $msgdetails = msgdetails($V_type, $arr); ?>
                    <tr>
                        <td><input type='checkbox' name='del[]' value='<?= $arr['id'] ?>' /></td>
                        <td><?= $msgdetails['unread'] ?></td>
                        <td><?= $msgdetails['sender'] ?></td>
                        <td><?= $msgdetails['receiver'] ?></td>
                        <td><?= $msgdetails['subject'] ?></td>
                        <td><?= $msgdetails['added'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <tbody>
        </table>
    </div>

    <div style="float: left;">
        <?= LANG["READ"] ?>&nbsp;<i class='fa fa-file-text' title='<?= LANG["READ"] ?>'></i>&nbsp;
        <?= LANG["UNREAD"] ?>&nbsp;<i class='fa fa-file-text tticon-red' title='<?= LANG["UNREAD"] ?>'></i>
    </div>

    <div class='text-center'>
        <button type="submit" class="btn ttbtn" value='Delete Checked' name='delete'><?= LANG["DEL"] ?></button>
        <?php if ($V_type == 'inbox') { ?>
            <button type="submit" class="btn ttbtn" value='Read Checked' name='read'><?= LANG["READ"] ?></button>
        <?php } ?>
    </div>
</form>

<?= $V_pager ?>
<?php block_end(); ?>