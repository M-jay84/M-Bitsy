<?php block_begin($V_title) ?>
<form name="upload" enctype="multipart/form-data" action="<?= Url('adminimport/submit') ?>" method="post">
    <input type="hidden" name="takeupload" value="yes" />
    <div class="tt_blockcenter rounded p-3">
        <div class="text-center">
            <?php if (count($V_files) == 1) : ?>
                <p><?= Txt("FILES") ?></p>
                <?php foreach ($V_files as $f) : ?>
                    <?= htmlspecialchars($f) ?><br />
                <?php endforeach ?>
            <?php else : ?>
                <p><?= Txt("ADD_IMPORT_FILES") ?></p>
            <?php endif; ?>

            <?= Txt("CATEGORY") ?>
            <select name='type' style='width: 95%' class='form-control'>
                <option value='0'>(<?= Txt("CHOOSE_ONE") ?>)</option>
                <?= $V_categorys ?>
            </select><br>
            <?= Txt("LANGUAGE") ?>
            <?= $V_languages ?><br>

            <?php $anonycheck = ''; ?>
            <?php if (!CONFIG['ANONYMOUSUPLOAD']) : ?>
                <?= Txt("UPLOAD_ANONY") ?>
                <?php printf("<input name='anonycheck' value='yes' type='radio' " . ($anonycheck ? " checked='checked'" : "") . " />Yes <input name='anonycheck' value='no' type='radio' " . (!$anonycheck ? " checked='checked'" : "") . " />No"); ?> &nbsp;<?= Txt("UPLOAD_ANONY_MSG") ?><br>
            <?php endif ?>
            <br>
            <div class="text-center">
                <button type="submit" class="btn ttbtn btn-sm"><?= Txt("UPLOAD") ?></button>
            </div>
        </div>
</form>
<?php block_end() ?>