<?php block_begin($page_title) ?>
<div class="text-center">
    <a href='<?= Url('browse') ?>'><?= LANG["BROWSE_TORRENTS"] ?></a> - 
    <a href='<?= Url('search') ?>'><?= LANG["SEARCH_TORRENTS"] ?></a>
</div>
<?php torrenttable($V_tags);
block_end();