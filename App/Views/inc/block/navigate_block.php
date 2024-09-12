<?php

if ($_SESSION['id'] || !CONFIG['MEMBERSONLY']) {
    block_begin(LANG["NAVIGATION"]);

    if ($_SESSION['id']) { ?>
        <div class='p-2'>
            <a href="<?= Url() ?>"><b><?= LANG["HOME"] ?></b></a><br>

            <div class="dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown"><b><?= LANG["PROFILE"] ?></b></a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a href="<?= Url("profile/". $_SESSION['id'] ."") ?>" class="dropdown-item"><?= LANG["PROFILE"] ?></a>
                    <a href="<?= Url("peer/seeding/User(id]") ?>" class="dropdown-item"><?= LANG["SEEDING"] ?></a>
                    <a href='<?= Url("peer/leeching/User(id]") ?>' class="dropdown-item"><?= LANG["DOWNLOADING"] ?></a>
                    <a href="<?= Url('bonus') ?>" class="dropdown-item"><?= LANG["BONUS"] ?></a>
                    <a href="<?= Url('invite') ?>" class="dropdown-item"><?= LANG["INVITES"];?></a>
                </div>
            </div>
            <?php
            if ($_SESSION['view_torrents'] == "yes" || $_SESSION['view_torrents'] == "no" && CONFIG['MEMBERSONLY']) { ?>
                <div class="dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown"><b><?= LANG["TORRENT"] ?></b></a>
                <div class="dropdown-menu dropdown-menu-end">
                        <!-- Check the link! -->
                        <a href="<?= Url('browse') ?>" class="dropdown-item"><?= LANG["BROWSE_TORRENTS"] ?></a> <?php
                        if ($_SESSION['class'] < CONFIG['_VIP'] && CONFIG['UPLOADERSONLY']) { ?>
                            <?php
                        } else { ?>
                            <a href="<?= Url('upload') ?>" class="dropdown-item"><?= LANG["UPLOAD_TORRENT"] ?></a> <?php
                        } ?>
                        <a href="<?= Url('search') ?>" class="dropdown-item"><?= LANG["SEARCH_TORRENTS"] ?></a>
                         <?php
                        if (CONFIG['_TMDBswitch']) { ?>
                            <a href="<?= Url('moviedatabase') ?>" class="dropdown-item"><?= LANG["TMDB"] ?></a> <?php
                        } ?>
                    </div>
                </div>
            <?php
            } ?>
                <div class="dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown"><b><?= LANG["FORUMS"] ?></b></a>
                <div class="dropdown-menu dropdown-menu-end">
                <a href="<?= Url('forum') ?>" class="dropdown-item"><?= LANG["FORUMS"] ?></a>
                    <a href="<?= Url('forum/viewunread') ?>" class="dropdown-item"><?= LANG["NEW_POSTS"] ?></a>
                    <a href="<?= Url('forum/search') ?>" class="dropdown-item"><?= LANG["SEARCH"] ?></a>
                </div>
            </div>
            <div class="dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown"><b><?= LANG["MEMBERS"] ?></b></a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a href="<?= Url('members') ?>" class="dropdown-item"><?= LANG["MEMBERS"] ?></a>
					<a href="<?= Url('topten') ?>" class="dropdown-item"><?= LANG["TOP_10"] ?></a>
                </div>
            </div>
            <div class="dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown"><b><?= LANG["STAFF"] ?></b></a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a href="<?= Url('staff') ?>" class="dropdown-item"><?= LANG["STAFF"] ?></a>
                    <a href="<?= Url('contact') ?>" class="dropdown-item"><?= LANG["CONTACT_US"] ?></a>
                </div>
            </div>
            <div class="dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown"><b><?= LANG["INFO"] ?></b></a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a href="<?= Url('rule') ?>" class="dropdown-item"><?= LANG["RULES"] ?></a>
				</div>
            </div>
        </div>
        <?php
    }
    block_end();
}