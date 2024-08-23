<?php

use App\Libs\DB;

// Page Column Blocks
function column($align = 'middle')
{
    $namepace = "\\App\\Libs\\Cache";
    $TTCache = new $namepace();
    if (($blocks = $TTCache->get("block/blocks_$align", 900)) === false) {
        $res = DB::fetchAll('blocks', '*', ['position =', 'enabled ='], [$align,1], 'ORDER BY sort');
        $blocks = array();
        foreach ($res as $result) {
            $blocks[] = $result["name"];
        }
        $TTCache->Set("block/blocks_$align", $blocks, 900);
    }
    foreach ($blocks as $blockfilename) {
        $url = $_GET['url'] ?? '';
        if (!in_array($url, CONFIG['ISURL']) && $align != 'middle') {
            include "../App/Views/block/" . $blockfilename . "_block.php";
        }
    }
}

// Begin Page/Block Frame
function block_begin($caption = "-", $p = 3)
{
    ?>
    <div class="tt_block rounded">
        <div class="tt_blockhead text-center">
            <?= $caption ?>
        </div>
        <div class="p-<?= $p ?>">
    <?php
}

// End Page/Block Frame
function block_end()
{
    ?>
        </div>
    </div>
    <?php
}