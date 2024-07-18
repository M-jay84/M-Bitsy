<?php block_begin($V_title) ?>
<div class="text-center">
    <a href='<?= Url('browse') ?>'><?= Txt("BROWSE_TORRENTS") ?></a> - 
    <a href='<?= Url('search') ?>'><?= Txt("SEARCH_TORRENTS") ?></a>
</div>
<?php torrenttable($USER, $V_tags);
block_end();