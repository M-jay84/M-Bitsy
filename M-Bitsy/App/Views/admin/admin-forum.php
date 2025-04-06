<?php block_begin($TITLE) ?>
<div class="rounded">
    <div class="text-center">
        <a type="button" class="model_form ttbtn btn-sm" href="<?= URLROOT ?>/adminforumcat"><?= LANG["BACK"] ?></a>
        <a type="button" class="model_form ttbtn btn-sm" data-bs-toggle="modal" data-bs-target="#forumModal"><?= LANG["ADD"] ?></a>
    </div>
    <div class="table-responsive p-1">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?= LANG["ID"] ?></th>
                    <th><?= LANG["TITLE"] ?></th>
                    <th><?= LANG["SORT"] ?></th>
                    <th><?= LANG["MOVE"] ?></th>
                    <th></th>
                </tr>
            </thead>
            <?php foreach ($STMT as $row) :
                foreach ($CATS as $cat) {
                    if ($cat['id'] == $row['category']) {
                        $category = $cat['name'];
                    }
                } ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['name'] ?><br><small><?php echo format_comment($row['description']) ?></small></td>
                    <td><?= $row['sort'] ?></td>
                    <td>
                        <?php
                        $move = $row['sort'] == 1 ? 
                            "<a href='".URLROOT."/adminforum/$row[category]/$row[id]/down/&sort=$row[sort]'><i class='fa fa-arrow-down'></i></a>" : 
                            "<a href='".URLROOT."/adminforum/$row[category]/$row[id]/up/&sort=$row[sort]'><i class='fa fa-arrow-up'></i></a>  
                            <a href='".URLROOT."/adminforum/$row[category]/$row[id]/down/&sort=$row[sort]'><i class='fa fa-arrow-down'></i></a>";
                        echo $move
                        ?>
                    </td>
                    <td>
                        <a href="javascript:;" class="forumAttr" data-bs-toggle="modal" data-bs-target="#forumModal" data-id="<?= $row['id'] ?>" data-name="<?= $row['name'] ?>" data-sort="<?= $row['sort'] ?>" data-description="<?= $row['description'] ?>">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href='<?= Url("adminforum/delete/$row[id]") ?>'>
                            <i class='fa fa-trash tticon-red' title='<?= LANG["DEL"]  ?>'></i>
                        </a>
                    </td>
                </tr><br>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $('.forumAttr').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var sort = $(this).data('sort');
        var description = $(this).data('description');
        var minclassread = $(this).data('minclassread');
        var minclasswrite = $(this).data('minclasswrite');
        var guest_read = $(this).data('guest_read');

        $('#id').val(id);
        $('#name').val(name);
        $('#sort').val(sort);
        $('#description').val(description);
        $('#minclassread').val(minclassread);
        $('#minclasswrite').val(minclasswrite);
        $('#guest_read').val(guest_read);
    });
</script>

<form id="submit" method="post" action="<?= Url('adminforum/submit') ?>">
    <div class="modal fade" id="forumModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
            <div class="modal-content">
                <div class="form-floating mb-4">
                    <input type='hidden' name='id' id='id' />
                    <?= LANG["NAME"] ?>
                    <input type='text' class="form-control fullwidth" name='name' id='name'>
                    <?=  LANG["SORT"] ?>
                    <input type='text' name='sort' id='sort' class="form-control fullwidth">
                    <?= LANG["DESC"] ?>
                    <textarea name='description' id='description' class="form-control"></textarea>
                    <?= LANG["CAT"] ?>
                    <select name='new_forum_cat' class='form-select'>
                        <?php foreach ($CATS as $row) : ?>
                            <option value='<?= $row['id'] ?>'><?= $row['name'] ?></option>
                        <?php endforeach ?>
                    </select>
                    <?= LANG["SUB_FORUM_HELP"] ?>
                    <select name='sub' id='sub' class='form-select'>
                        <option value=0><?= LANG["NONE"] ?></option>
                        <?php foreach ($FORUM as $row) { ?>
                            <option value='<?= $row['id'] ?>'><?= $row['name'] ?></option>
                        <?php } ?>
                    </select>
                    <?= LANG["MIN_CLASS_READ"] ?>
                    <select name='minclassread' id='minclassread' class='form-select'>
                        <?php foreach ($GROUPS as $group) { ?>
                            <option value='<?= $group['group_id'] ?>'><?= $group['level'] ?></option>
                        <?php } ?>
                    </select>
                    <?= LANG["MIN_CLASS_POST"] ?>
                    <select name='minclasswrite' id='minclasswrite' class='form-select'>
                         <option value='1'>Member</option>
                    </select>
                    <?= LANG["FORUM_ALLOW_GUEST_READ"] ?>
                    <input type="radio" name="guest_read" id="guest_read" value="yes" checked='checked' /><?= LANG["YES"] ?>, <input type="radio" name="guest_read" value="no" /> <?= LANG["NO"] ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                    <button type="submit" class="btn btn-sm ttbtn"><?= LANG['SUBMIT']; ?></button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php block_end() ?>