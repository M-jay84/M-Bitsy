<?php block_begin($page_title) ?>
<div class='table-responsive'>
    <div class='text-center'>
        <?php echo sprintf(LANG['SEEDBONUS_MESSAGE'], $current_user->seedbonus) ?>
    </div>
    <table class='table table-striped'>
        <thead>
            <tr>
                <th><?= LANG["OPTION"] ?></th>
                <th><?= LANG["TYPE"] ?></th>
                <th><?= LANG["POINTS"] ?></th>
                <th><?= LANG["EXCHANGE"] ?></th>
                <?php if ($current_user->isStaff) : ?>
                    <th><?= LANG["STAFF"] ?></th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bonus_result as $row) : ?>
                <form method="post" action="<?= Url('bonus/submit') ?>">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>" />
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['descr']) ?></td>
                        <td><?= $row['cost'] ?></td>
                        <td><input type="submit" class='btn btn-sm ttbtn' value="<?= LANG['SUBMIT'] ?>"></td>
                        <?php if ($current_user->isStaff) : ?>
                        <td>
                            <a href="<?= Url("bonus/delete/$row[id]") ?>"><i class="fa fa-trash"></i></a>
                            <a href="javascript:;" class="bonusAttr" data-bs-toggle="modal" data-bs-target="#bonusModal" 
                            data-id="<?= $row['id'] ?>" 
                            data-title="<?= $row['title'] ?>"
                            data-cost="<?= $row['cost'] ?>" 
                            data-type="<?= $row['type'] ?>"
                            data-descr="<?= $row['descr'] ?>"
                            data-value="<?= $row['value'] ?>" title="<?= LANG['EDIT'] ?>"><i class="fa fa-pencil"></i></a>
                        </td>
                        <?php endif; ?>
                    </tr>
                </form>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class='text-center'>
        <?php echo sprintf(LANG["SEEDBONUS_REWARD"], CONFIG['BONUSPERTIME'], $set_bonus_interval ); ?>
    </div>
</div>
<?php block_end() ?>

<?php if ($current_user->isStaff) : ?>
    <p class="text-end">
       <button class="ttbtn"><i class="fa fa-user-secret" aria-hidden="true" title="Admin"></i><a href="#" data-bs-toggle="modal" data-bs-target="#bonusModal"><?= LANG["ADD"] ?></a></button>
    <p>

<script>
    $('.bonusAttr').click(function() {
        var id = $(this).data('id');
        var title = $(this).data('title');
        var cost = $(this).data('cost');
        var type = $(this).data('type');
        var descr = $(this).data('descr');
        var value = $(this).data('value');
        $('#id').val(id);
        $('#title').val(title);
        $('#cost').val(cost);
        $('#type').val(type);
        $('#descr').val(descr);
        $('#value').val(value);
    });
</script>

<!-- Add Bonus Modal -->
<form id="submit" method="post" action="<?= Url('bonus/update') ?>">
    <div class="modal fade" id="bonusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-body">
                    <input type='hidden' name='id' id="id"/>
                    <?= LANG["TITLE"] ?>
                    <input type='text' name="title" id="title" class="form-control fullwidth" /><br>
                    <?= LANG["POINTS"] ?>
                    <input type='text' name="cost" id="cost" class="form-control fullwidth" /><br>
                    <?= LANG["TYPE"] ?>
                    <select name="type" id="type" class='form-select'>
                        <?php foreach (array('invite', 'traffic', 'VIP', 'other', 'HnR') as $type) : ?>
                            <option name="type"><?= $type; ?></option>
                        <?php endforeach; ?>
                    </select><br>
                    <?= LANG["VALUE"] ?>
                    <input type='text' name="value" id="value" class="form-control fullwidth" /><br>
                    <?= LANG["DESCRIPTION"] ?>
                    <textarea name="descr" id="descr" class="form-control" style="height: 150px;"></textarea><br>
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