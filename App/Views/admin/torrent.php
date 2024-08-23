<?php block_begin($page_title) ?>
<form method='get' action='<?= Url('admintorrent') ?>'>
    <p class="tt_blockcenter rounded text-center">
        Search: <input type='text' name='search' value='<?= $V_search ?>' size='30' />
        <input type='submit' value='Search' />
    </p>
</form>

<p class="text-center">
    <a href='<?= Url('admintorrent') ?>'><?= LANG["ALL"] ?> - </a>&nbsp;&nbsp;
    <a href='<?= Url('admintorrent/dead') ?>'><?= LANG["DEAD"] ?> - <?= LANG["TORRENT"] ?></a>&nbsp;&nbsp;
    <a href='<?= Url('admintorrent/banned') ?>'><?= LANG["BANNED"] ?> - <?= LANG["TORRENT"] ?></a>&nbsp;&nbsp;
    <a href='<?= Url('admintorrent/free') ?>'><?= LANG["FREELEECH"] ?> - <?= LANG["TORRENT"] ?></a><br>
    <?= $_GET['type'] ?> - <?= LANG["TORRENT"] ?> - <?= LANG["TOTAL"] ?> <?= $V_count ?>
</p>

<form id="delete" method='post' action='<?= Url('admintorrent/delete') ?>'>
    <?php torrenttable($V_torrents); ?>
    <p class="text-center">
        <input type='submit' class='btn btn-sm ttbtn' value='Delete checked' />
    </p>
</form>
<?= $V_pager ?>
<?php block_end() ?>