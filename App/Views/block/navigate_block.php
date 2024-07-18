<?php

if ($USER->has('id') || !CONFIG['MEMBERSONLY']) {
    block_begin(Txt("NAVIGATION"));

    if ($USER->has('id')) { ?>
        <div class='p-2'>
            <a href="<?= Url() ?>"><b><?= Txt("HOME") ?></b></a><br>

            <div class="dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown"><b><?= Txt("PROFILE") ?></b></a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a href="<?= Url("profile/". $USER->has('id') ."") ?>" class="dropdown-item"><?= Txt("PROFILE") ?></a>
                    <a href="<?= Url("peer/seeding/User(id]") ?>" class="dropdown-item"><?= Txt("SEEDING") ?></a>
                    <a href='<?= Url("peer/leeching/User(id]") ?>' class="dropdown-item"><?= Txt("DOWNLOADING") ?></a>
                    <a href="<?= Url("friend/". $USER->has('id') ."") ?>" class="dropdown-item"><?= Txt("FRIENDS") ?></a>
                    <a href="<?= Url('bonus') ?>" class="dropdown-item"><?= Txt("BONUS") ?></a>
                    <a href="<?= Url('invite') ?>" class="dropdown-item"><?= Txt("INVITES");?></a>
                </div>
            </div>
            <?php
            if ($USER->permission('view_torrents')) { ?>
                <div class="dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown"><b><?= Txt("TORRENT") ?></b></a>
                <div class="dropdown-menu dropdown-menu-end">
                        <!-- Check the link! -->
                        <a href="<?= Url('browse') ?>" class="dropdown-item"><?= Txt("BROWSE_TORRENTS") ?></a> <?php
                        if ($USER->has('class') < _VIP && CONFIG['UPLOADERSONLY']) { ?>
                            <?php
                        } else { ?>
                            <a href="<?= Url('upload') ?>" class="dropdown-item"><?= Txt("UPLOAD_TORRENT") ?></a> <?php
                        } ?>
                        <a href="<?= Url('search') ?>" class="dropdown-item"><?= Txt("SEARCH_TORRENTS") ?></a>
                        <a href="<?= Url('catalog') ?>" class="dropdown-item"><?= Txt("CATALOG") ?></a> <?php
                        if (_TMDBswitch) { ?>
                            <a href="<?= Url('moviedatabase') ?>" class="dropdown-item"><?= Txt("TMDB") ?></a> <?php
                        }
                        if (CONFIG["REQUESTSON"]) { ?>
                            <a href="<?= Url('request') ?>" class="dropdown-item"><?= Txt("REQUEST") ?></a><?php
                        } ?>
                        <a href="<?= Url('today') ?>" class="dropdown-item"><?= Txt("TODAYS_TORRENTS") ?></a>
                        <a href="<?= Url('needseed') ?>" class="dropdown-item"><?= Txt("NEED_SEED") ?></a>
                    </div>
                </div>
            <?php
            } ?>
                <div class="dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown"><b><?= Txt("FORUMS") ?></b></a>
                <div class="dropdown-menu dropdown-menu-end">
                <a href="<?= Url('forum') ?>" class="dropdown-item"><?= Txt("FORUMS") ?></a>
                    <a href="<?= Url('forum/viewunread') ?>" class="dropdown-item"><?= Txt("NEW_POSTS") ?></a>
                    <a href="<?= Url('forum/search') ?>" class="dropdown-item"><?= Txt("SEARCH") ?></a>
                </div>
            </div>
            <div class="dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown"><b><?= Txt("MEMBERS") ?></b></a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a href="<?= Url('members') ?>" class="dropdown-item"><?= Txt("MEMBERS") ?></a>
                    <a href="<?= Url('team') ?>" class="dropdown-item"><?= Txt("Team") ?></a>
					<a href="<?= Url('topten') ?>" class="dropdown-item"><?= Txt("TOP_10") ?></a>
                </div>
            </div>
            <div class="dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown"><b><?= Txt("STAFF") ?></b></a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a href="<?= Url('staff') ?>" class="dropdown-item"><?= Txt("STAFF") ?></a>
                    <a href="<?= Url('contact') ?>" class="dropdown-item"><?= Txt("CONTACT_US") ?></a>
                </div>
            </div>
            <div class="dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown"><b><?= Txt("INFO") ?></b></a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a href="<?= Url('rule') ?>" class="dropdown-item"><?= Txt("RULES") ?></a>
                    <a href="<?= Url('faq') ?>" class="dropdown-item"><?= Txt("FAQ") ?></a>
				</div>
            </div>
        </div>
        <?php
    }
    block_end();
}