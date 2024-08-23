<?php block_begin($page_title) ?>
<form name="upload" enctype="multipart/form-data" action="<?= Url('adminimport/submit') ?>" method="post">
    <input type="hidden" name="takeupload" value="yes" />
    <div class="tt_blockcenter rounded p-3">
        <div class="text-center">
            <?php if (count($V_files) == 1) : ?>
                <p><?= LANG["FILES"] ?></p>
                <?php foreach ($V_files as $f) : ?>
                    <?= htmlspecialchars($f) ?><br />
                <?php endforeach ?>
            <?php else : ?>
                <p><?= LANG["ADD_IMPORT_FILES"] ?></p>
            <?php endif; ?>

            <?= LANG["CATEGORY"] ?>
            <select name='type' style='width: 95%' class='form-control'>
                <option value='0'>(<?= LANG["CHOOSE_ONE"] ?>)</option>
                <?= $V_categorys ?>
            </select><br>
            <?= LANG["LANGUAGE"] ?>
            <?= $V_languages ?><br>

            <?php $anonycheck = ''; ?>
            <?php if (!CONFIG['ANONYMOUSUPLOAD']) : ?>
                <?= LANG["UPLOAD_ANONY"] ?>
                <?php printf("<input name='anonycheck' value='yes' type='radio' " . ($anonycheck ? " checked='checked'" : "") . " />Yes <input name='anonycheck' value='no' type='radio' " . (!$anonycheck ? " checked='checked'" : "") . " />No"); ?> &nbsp;<?= LANG["UPLOAD_ANONY_MSG"] ?><br>
            <?php endif ?>
            <br>
            <div class="text-center">
                <button type="submit" class="btn ttbtn btn-sm"><?= LANG["UPLOAD"] ?></button>
            </div>
        </div>
</form>
<?php block_end() ?>