<?php block_begin($TITLE) ?>
<form method='get' action='<?= Url('admintorrent') ?>'>
    <p class="tt_blockcenter rounded text-center">
        <?= LANG["SEARCH"] ?>: <input type='text' name='search' value='<?= $SEARCH ?>' size='30' />
        <input type='submit' value='Search' />
    </p>
</form>

<p class="text-center">
    <a href='<?= Url('admintorrent') ?>'><?= LANG["ALL"] ?></a>&nbsp;-&nbsp;
    <a href='<?= Url('admintorrent/dead') ?>'><?= LANG["DEAD"] ?> </a>&nbsp;-&nbsp;
    <a href='<?= Url('admintorrent/banned') ?>'><?= LANG["BANNED"] ?></a>&nbsp;-&nbsp;
    <a href='<?= Url('admintorrent/free') ?>'><?= LANG["FREELEECH"] ?></a><br>
    <?= LANG["TORRENT"] ?> - <?= LANG["TOTAL"] ?> <?= $COUNT ?> - <?= $TYPE ?>
</p>

<form id="delete" method='post' action='<?= Url('admintorrent/delete') ?>'>
    <?php torrenttable($TORRENTS); ?>
    <p class="text-center">
        <input type='submit' class='btn btn-sm ttbtn' value='Delete checked' />
    </p>
</form>
<?= $PAGER ?>
<?php block_end() ?>