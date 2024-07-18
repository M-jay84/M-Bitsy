<?php block_begin($V_title) ?>
<p class="text-center">
    <a type="button" class="model_form ttbtn btn-sm" data-bs-toggle="modal" data-bs-target="#addModal"><?= Txt('ADD') ?></a>
<p>

<div class="table-responsive p-2">
    <p class="text-center">
        <i><?= Txt('CAT_IMAGE_HELP') ?></i>
    </p>
    <table class='table table-striped'>
        <thead>
            <tr>
                <th width='10'><?= Txt('SORT') ?></th>
                <th><?= Txt('PARENT_CAT') ?></th>
                <th><?= Txt('SUB_CAT') ?></th>
                <th><?= Txt('ICON') ?></th>
                <th><?= Txt('ACTION') ?></th>
            </tr>
        </thead>
        <tbody><?php
                foreach ($V_cats as $row) { ?>
                <tr>
                    <td><?= $row['sort_index'] ?></td>
                    <td><?= $row['parent_cat'] ?></td>
                    <td><?= $row['name'] ?></td>
                    <td> <?php
                            if (isset($row["image"]) && $row["image"] != "") {
                                print("<i class='fa " . $row['image'] . " title='" . $row['name'] . "''></i>");
                            } else {
                                print("-");
                            } ?>
                    </td>
                    <td>
                        <a href="javascript:;" class="addAttr" data-bs-toggle="modal" data-bs-target="#addModal" 
                            data-id="<?= $row['id'] ?>" 
                            data-sort_index="<?= $row['sort_index'] ?>" 
                            data-parent_cat="<?= $row['parent_cat'] ?>" 
                            data-name="<?= $row['name'] ?>" 
                            data-image="<?= $row['image'] ?>">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href='<?= Url("admincategorie/delete/$row[id]"); ?>'>
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr> <?php
                } ?>
        </tbody>
    </table>
</div>

<script>
    $('.addAttr').click(function() {
        var id = $(this).data('id');
        var sort_index = $(this).data('sort_index');
        var parent_cat = $(this).data('parent_cat');
        var name = $(this).data('name');
        var image = $(this).data('image');

        $('#id').val(id);
        $('#sort_index').val(sort_index);
        $('#parent_cat').val(parent_cat);
        $('#name').val(name);
        $('#image').val(image);
    });
</script>

<!-- The Modal In Form -->
<form id="submit" method="post" action="<?= Url('admincategorie') ?>">
    <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-body">
                    <input type='hidden' name='id' id='id' />
                    <input type='text' name='parent_cat' id='parent_cat' class="form-control fullwidth" placeholder="<?= Txt('PARENT_CAT') ?>"/><br>
                    <input type='text' name='name' id='name' class="form-control fullwidth" placeholder="<?= Txt('SUB_CAT') ?>"/><br>
                    <input type='text' name='sort_index' id='sort_index' class="form-control fullwidth" placeholder="<?= Txt('SORT') ?>"/><br>
                    <input type='text' name='image' id='image' class="form-control fullwidth" placeholder="<?= Txt('ICON') ?> eg fa-book"/><br>

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