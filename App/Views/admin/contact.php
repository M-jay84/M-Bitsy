<?php block_begin($V_title) ?>
<form id="delete" method="post" action="<?= Url('admincontact/delete') ?>">
    <div class='table-responsive p-2'>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th><?= Txt("SUBJECT") ?></th>
                    <th><?= Txt("SENDER") ?></th>
                    <th><?= Txt("ADDED") ?></th>
                    <th><?= Txt("ANSWERED") ?></th>
                    <th><?= Txt("DEL") ?> <input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($V_messages as $row) : ?>
                    <?php
                    if ($row['answered']) {
                        $answered = "<font color=green><b>Yes - <b>" . coloredname($row['answeredby']) . "</b> 
                                    (<a href='javascript:;' class='answerAttr' data-bs-toggle='modal' data-bs-target='#answerModal' 
                                    data-answer='" . $row['answer'] . "' >" . Txt("ANSWER") . "</a>)</b></font>";
                    } else {
                        $answered = "<font color=red><b>" . Txt("NO") . "</b></font>";
                    }
                    ?>
                    <tr>
                        <td>
                            <a href="javascript:;" class="contactAttr" data-bs-toggle="modal" data-bs-target="#contactModal" data-id="<?= $row['id'] ?>" data-sender="<?= $row['sender'] ?>" data-subject="<?= $row['subject'] ?>" data-msg="<?= $row['msg'] ?>">
                                <b><?= $row['subject']; ?></b>
                            </a>
                        </td>
                        <td><b><?= coloredname($row['sender']); ?></b></td>
                        <td><?= $row['added']; ?></td>
                        <td><?= $answered; ?></td>
                        <td><input type="checkbox" name="ids[]" value="<?= $row['id']; ?>"></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div><br>

    <div class="text-center">
        <button type="submit" class="btn ttbtn btn-sm"><?= Txt("SUBMIT"); ?></button>
    </div>
</form>

<script>
    $('.contactAttr').click(function() {
        var id = $(this).data('id');
        var subject = $(this).data('subject');
        var msg = $(this).data('msg');
        var sender = $(this).data('sender');

        $('#id').val(id);
        $('#subject').val(subject);
        $('#msg').val(msg);
        $('#sender').val(sender);
    });
</script>

<!-- The Modal In Form -->
<form id="submit" method="post" action="<?= Url('admincontact') ?>">
    <div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-body">
                    <p class="text-center">
                        <input type='hidden' name='id' id='id' />
                        <input type='hidden' name='sender' id='sender' />
                        <b><input type='text' name='subject' id='subject' class='form-control fullwidth' readonly /></b><br>
                        <textarea type='text' name='msg' id='msg' class='form-control' style='height: 150px;' readonly></textarea><br><br>
                        <textarea type='text' name='response' class='form-control' style='height: 150px;' placeholder="<?= Txt("ANSWER") ?>"></textarea><br><br>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= Txt("CLOSE") ?></button>
                    <button type="submit" class="btn btn-sm ttbtn"><?= Txt("SAVE") ?></button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $('.answerAttr').click(function() {
        var answer = $(this).data('answer');

        $('#answer').val(answer);
    });
</script>
<!-- Answer Modal -->
<form id="submit" method="post" action="<?= Url('admincontact') ?>">
    <div class="modal fade" id="answerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-body">
                    <textarea type='text' class='form-control' style='height: 150px;' name='answer' id='answer' readonly></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= Txt("CLOSE") ?></button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php block_end() ?>