<?php
if (CONFIG['SITENOTICEON']) {
    block_begin(LANG['NOTICE']);
    ?>
    <div class="text-center">
        <?= CONFIG['SITENOTICE'] ?>
    </div>
    <?php
    block_end();
}