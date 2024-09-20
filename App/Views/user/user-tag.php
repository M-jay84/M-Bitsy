<?php block_begin($TITLE) ?>
<div class="text-center">
    <a href='<?= Url('browse') ?>'><?= LANG["BROWSE_TORRENTS"] ?></a>
</div>
<?php torrenttable($TAGS);
block_end();