<?php block_begin($V_title) ?>
<form method='get' action='<?= Url('admintorrent') ?>'>
    <p class="tt_blockcenter rounded text-center">
        Search: <input type='text' name='search' value='<?= $V_search ?>' size='30' />
        <input type='submit' value='Search' />
    </p>
</form>

<p class="text-center">
    <a href='<?= Url('admintorrent') ?>'><?= Txt("ALL") ?> - </a>&nbsp;&nbsp;
    <a href='<?= Url('admintorrent/dead') ?>'><?= Txt("DEAD") ?> - <?= Txt("TORRENT") ?></a>&nbsp;&nbsp;
    <a href='<?= Url('admintorrent/banned') ?>'><?= Txt("BANNED") ?> - <?= Txt("TORRENT") ?></a>&nbsp;&nbsp;
    <a href='<?= Url('admintorrent/free') ?>'><?= Txt("FREELEECH") ?> - <?= Txt("TORRENT") ?></a><br>
    <?= $_GET['type'] ?> - <?= Txt("TORRENT") ?> - <?= Txt("TOTAL") ?> <?= $V_count ?>
</p>

<form id="delete" method='post' action='<?= Url('admintorrent/delete') ?>'>
    <?php torrenttable($USER, $V_torrents); ?>
    <p class="text-center">
        <input type='submit' class='btn btn-sm ttbtn' value='Delete checked' />
    </p>
</form>
<?= $V_pager ?>
<?php block_end() ?>