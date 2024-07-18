<?php block_begin($V_title) ?>
<div class="text-center">
    <a type="button" class="model_form ttbtn btn-sm" data-bs-toggle="modal" data-bs-target="#messageModal"><?= Txt('CREATE_MASS') ?></a>
<div>
    
<?php if ($USER->has('class') >= _SUPERMODERATOR) : ?>
  <form id='delete' method='post' action='<?= Url('adminmessage/delete') ?>'>
    <div class='table-responsive p-1'>
        <table class='table table-striped'>
            <thead>
                <tr>
                <th><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
                    <th><?= Txt('SENDER') ?></th>
                    <th><?= Txt('RECEIVER') ?></th>
                    <th><?= Txt('ADDED') ?></th>
                    <th><?= Txt('TEXT') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($V_messages as $arr) : ?>
                    <tr>
                    <td col=1><input type='checkbox' name='ids[]' value='<?= $arr['id'] ?>' /></td>
                        <td col=1><?= coloredname($arr["sender"]) ?></td>
                        <td col=1><?= coloredname($arr["receiver"]) ?></td>
                        <td col=1><?= utc_to_tz($arr["added"]) ?></td>
                        <td col=5><?= strip_tags($arr["msg"]) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div><br>
    <p class="text-center">
        <input type='submit' class='btn btn-sm ttbtn' value='<?= Txt('DEL') ?>' />
    </p>
  </form>
  
   <?= $V_pager ?>

<?php else : ?>
    <p class="text-center"><?= Txt('MESSAGE_SPY') ?> <?= _SUPERMODERATOR ?></p>
<?php endif; ?>

<form id="submit" method="post" action="<?= Url('adminmessage') ?>">
    <div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-body">
                    <?= Txt('SEND_TO') ?><br>
                    <?php foreach ($V_groups as $row) : ?>
                        <input type='checkbox' name='clases[]' value='<?= $row['group_id']; ?>?' /><?= $row['level'] ?><br><br>
                    <?php endforeach; ?>
                    <div class="input-group mb-3">
                        <?= Txt("SENDER") ?> &nbsp;
                        <?= $USER->has('username') ?>
                        <input name="sender" type="radio" value="self" checked="checked" /> &nbsp;
                        <span></span>
                        <?= Txt('SYSTEM') ?>
                        <input name="sender" type="radio" value="system" />
                    </div><br>
                    <input id="subject" type="text" class="form-control fullwidth" name="subject" placeholder="<?= Txt("SUBJECT") ?>"><br>
                    <?php echo textbbcode('submit', 'msg'); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= Txt('CLOSE') ?></button>
                    <button type="submit" class="btn btn-sm ttbtn"><?= Txt("SUBMIT") ?></button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php block_end() ?>