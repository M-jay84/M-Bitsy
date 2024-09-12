<?php foreach ($V_rules as $row) : ?>
    <?php if ($row['public'] == "yes") { ?>
        <?php block_begin($row['title']) ?>
        <?= format_comment($row['text']); ?><br>
        <?php if ($current_user->class > CONFIG['_UPLOADER']) : ?>
            <p class="text-end">
                <i class="fa fa-user-secret m-1 tt_prime" title="<?= LANG['STAFF'] ?>"></i>
                <a href='<?= Url("rule/delete/$row[id]") ?>'><i class="fa fa-trash"></i></a>
                <a href="javascript:;" class="rulesAttr" data-bs-toggle="modal" data-bs-target="#rulesModal" data-id="<?= $row['id'] ?>" data-title="<?= $row['title'] ?>" data-text="<?= $row['text'] ?>" data-public="<?= $row['public'] ?>" data-classs="<?= $row['class'] ?>">
                <i class="fa fa-pencil"></i></a>
            </p>
        <?php endif; ?>
        <?php block_end(); ?>
    <?php } else if ($row['public'] == "no" && $row['class'] <= $current_user->class) { ?>
        <?php block_begin($row['title']) ?>
        <?= format_comment($row['text']); ?><br>
        <?php if ($current_user->class > CONFIG['_UPLOADER']) : ?>
            <p class="text-end">
                <i class="fa fa-user-secret m-1 tt_prime" title="<?= LANG['STAFF']; ?>"></i>
                <a href='<?= Url("rule/delete/$row[id]") ?>'><i class="fa fa-trash"></i></a>
                <a href="javascript:;" class="rulesAttr" data-bs-toggle="modal" data-bs-target="#rulesModal" data-id="<?= $row['id'] ?>" data-title="<?= $row['title'] ?>" data-text="<?= $row['text'] ?>" data-public="<?= $row['public'] ?>" data-classs="<?= $row['class'] ?>">
                <i class="fa fa-pencil"></i></a>
            </p>
        <?php endif; ?>
        <?php block_end(); ?>
    <?php } ?>
<?php endforeach; ?>

<?php if ($current_user->class > CONFIG['_UPLOADER']) : ?>
    <p class="text-end">
        <button class="ttbtn"><i class="fa fa-user-secret m-1 tt_prime" data-bs-toggle="modal" data-bs-target="#rulesModal" title="<?= LANG['STAFF'] ?>"><?= LANG[' Add new'] ?></i></button>
    <p>
<?php endif; ?>

<!-- Add/Edit Modal -->
<?php if ($current_user->class > CONFIG['_UPLOADER']) : ?>
    <script>
        $('.rulesAttr').click(function() {
            var id = $(this).data('id');
            var title = $(this).data('title');
            var text = $(this).data('text');
            var public = $(this).data('public');
            var classs = $(this).data('classs');

            $('#id').val(id);
            $('#title').val(title);
            $('#text').val(text);
            $('#public').val(public);
            $('#classs').val(classs);
        });
    </script>

    <form id="submit" method="post" action="<?= Url('rule/edit') ?>">
        <div class="modal fade" id="rulesModal">
            <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
                <div class="modal-content">
                    <div class="modal-body">
                        <input type='hidden' name='id' id='id' />
                        <?= LANG['TITLE'] ?> :<br>
                        <input class="form-control fullwidth" type="text" name="title" id="title" />
                        <?= LANG['RULE'] ?> :<br>
                        <textarea style="height:150px;" class="form-control" name="text" id="text"></textarea>
                        <?= LANG['PUBLIC'] ?> :<br>
                        <select name='public' id='public' style='width: 95%' class='form-control'>
                            <option value='yes'><?= LANG['YES'] ?></option>
                            <option value='no'><?= LANG['NO'] ?></option>
                        </select>
                        <?= LANG['MIN_CLASS'] ?> :<br>
                        <input type="text" name='classs' id='classs' class="form-control" />
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