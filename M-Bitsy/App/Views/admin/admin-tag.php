<?php block_begin($TITLE) ?>
<p class="text-center">
    <a type="button" class="model_form ttbtn btn-sm" data-bs-toggle="modal" data-bs-target="#tagModal"><?= LANG['ADD'] ?></a>
<p>

<form id='delete' action='<?= Url('admintag/delete') ?>' method='post'>
    <div class='table-responsive  p-2'>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th><input type="checkbox" name="checkall" onclick="checkAll(this.form.id)" /></th>
                    <th><?= LANG['NAME'] ?></th>
                    <th><?= LANG['COLOUR'] ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($TAGS as $row) : ?>
                <tr>
                    <td>
                        <a href="javascript:;" class="tagAttr" data-bs-toggle="modal" data-bs-target="#tagModal" 
                           data-id="<?= $row['id'] ?>" 
                           data-name="<?= $row['name'] ?>" 
                           data-colour="<?= $row['colour'] ?>">
                            <i class="fa fa-edit"></i></a>&nbsp;
                        <input type='checkbox' name='ids[]' value='<?= $row['id'] ?>' />
                    </td>
                    <td><?= $row["name"] ?></td>
                    <td><?= $row["colour"] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="text-center">
        <button type="submit" class="btn btn-sm ttbtn"><?= LANG["DEL"] ?></button>
    </p>
</form>

<script>
    $('.tagAttr').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var colour = $(this).data('colour');

        $('#id').val(id);
        $('#name').val(name);
        $('#colour').val(colour);
    });
</script>

<!-- Add/Edit Modal -->
<form id="submit" method="post" action="<?= Url('admintag') ?>">
    <div class="modal fade" id="tagModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-body">
                    <input type='hidden' name='id' id="id" />
                    <?= LANG["NAME"] ?>
                    <input id="name" type="text" class="form-control fullwidth" name="name" minlength="3" maxlength="200">
                    <?= LANG["COLOUR"] ?>
                    <input id="colour" type="text" class="form-control fullwidth" name="colour" minlength="3" maxlength="200">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                    <button type="submit" class="btn btn-sm ttbtn"><?= LANG['SUBMIT'] ?></button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php block_end() ?>