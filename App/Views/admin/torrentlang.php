<?php block_begin($V_title) ?>
<p class="text-center">
    <a type="button" class="model_form ttbtn btn-sm" data-bs-toggle="modal" data-bs-target="#addModal"><?= Txt('ADD') ?></a><br />
    <i><?= Txt('LANG_IS_OPTIONAL') ?></i>
<p>
<div class='table-responsive '>
    <table class='table table-striped'>
        <thead>
            <tr>
                <th><b><?= Txt('ID') ?></b></th>
                <th><b><?= Txt('SORT') ?></b></th>
                <th><b><?= Txt("NAME") ?></b></th>
                <th><b><?= Txt('IMAGE') ?></b></th>
                <th><b><?= Txt('ACTION') ?></b></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($V_torrentlang as $row) : ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['sort_index'] ?></td>
                <td><?= $row['name'] ?></td>
                <td width='50' align='center'>
                    <?php if (isset($row["image"]) && $row["image"] != "") : ?>
                        <img src="<?= URLROOT ?>/assets/images/languages/<?= $row["image"] ?>" alt="<?= $row["name"] ?>" />
                    <?php else : ?>
                        -
                    <?php endif; ?>
                </td>
                <td>
                    <a href="javascript:;" class="addAttr" data-bs-toggle="modal" data-bs-target="#addModal" 
                       data-id="<?= $row['id'] ?>" 
                       data-image="<?= $row['image'] ?>" 
                       data-name="<?= $row['name'] ?>" 
                       data-sort_index="<?= $row['sort_index'] ?>"><i class="fa fa-edit"></i></a>
                    <a href='<?= Url("admintorrentlang/delete/$row[id]") ?>'><?= Txt('DEL') ?></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $('.addAttr').click(function() {
        var id = $(this).data('id');
        var sort_index = $(this).data('sort_index');
        var image = $(this).data('image');
        var name = $(this).data('name');

        $('#id').val(id);
        $('#sort_index').val(sort_index);
        $('#image').val(image);
        $('#name').val(name);
    });
</script>

<!-- Add/Edit Modal -->
<form id="submit" method="post" action="<?= Url('admintorrentlang') ?>">
    <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-body">
                    <input type='hidden' name='id' id="id" />
                    <?= Txt("NAME") ?>
                    <input id="name" type="text" class="form-control fullwidth" name="name" minlength="3" maxlength="25" required autofocus placeholder=""><br>
                    <?= Txt("SORT") ?>
                    <input id="sort_index" type="text" class="form-control fullwidth" name="sort_index"><br>
                    <?= Txt("IMAGE") ?>
                    <input id="image" type="text" class="form-control fullwidth" name="image"><br>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= Txt('CLOSE') ?></button>
                    <button type="submit" class="btn btn-sm ttbtn"><?= Txt("SUBMIT"); ?></button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php block_end() ?>