<?php block_begin($TITLE) ?>
<form id='messages' method='post' action='<?= Url("message/delete/$TYPE") ?>'>
    <div class='table-responsive  rounded'>
        <p class="text-center">
        <a href="<?= URLROOT ?>/message/mailbox/inbox"><button type="button" class="<?= activelink('inbox', 'inbox') ?>"><?= LANG["INBOX"] ?></button></a>&nbsp;
        <a href="<?= URLROOT ?>/message/mailbox/outbox"><button type="button" class="<?= activelink('outbox', 'outbox') ?>"><?= LANG["OUTBOX"] ?></button></a>&nbsp;
        <a href="<?= URLROOT ?>/message/create"><button type="button" class="<?= activelink('messages/create') ?>"><?= LANG["COMPOSE"] ?></button></a>
        </p>
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
                <?php foreach ($MESSAGES as $arr) :
                    $msgdetails = msgdetails($TYPE, $arr); ?>
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
        <?php if ($TYPE == 'inbox') { ?>
            <button type="submit" class="btn ttbtn" value='Read Checked' name='read'><?= LANG["READ"] ?></button>
        <?php } ?>
    </div>
</form>

<?= $PAGER ?>
<?php block_end(); ?>