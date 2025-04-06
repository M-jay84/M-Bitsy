<?php

if ($_SESSION['id'] || !CONFIG['MEMBERSONLY']) {
    block_begin(LANG["NAVIGATION"]);

    if ($_SESSION['id']) { ?>
        <div class='p-2'>
            <a href="<?= Url() ?>"><b><?= LANG["HOME"] ?></b></a><br>
            <?php
            if ($_SESSION['view_torrents'] == "yes" || $_SESSION['view_torrents'] == "no" && CONFIG['MEMBERSONLY']) :
                if ($_SESSION['class'] < CONFIG['_VIP'] && CONFIG['UPLOADERSONLY']) {
                } else { ?>
                    <a href="<?= Url('upload') ?>"><b><?= LANG["UPLOAD"] ?></b></a><br> <?php
                } ?>
                    <a href="<?= Url('browse') ?>"><b><?= LANG["SEARCH_TORRENTS"] ?></b></a><br>
            <?php
             endif; ?>
            <a href="<?= Url('forum') ?>"><b><?= LANG["FORUMS"] ?></b></a><br>
            <a href="<?= Url('members') ?>"><b><?= LANG["MEMBERS"] ?></b></a><br>
            <?php /*
			<a href="<?= Url('topten') ?>"><b><?= LANG["TOP_10"] ?></b></a><br>
            */ ?>
            <a href="<?= Url('rule') ?>"><b><?= LANG["RULES"] ?></b></a>
        </div>
        <?php
    }
    block_end();
}