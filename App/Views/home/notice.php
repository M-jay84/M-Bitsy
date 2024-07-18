<?php
if (CONFIG['SITENOTICEON']) {
    block_begin(Txt('NOTICE'));
    ?>
    <div class="text-center">
        <?= CONFIG['SITENOTICE'] ?>
    </div>
    <?php
    block_end();
}