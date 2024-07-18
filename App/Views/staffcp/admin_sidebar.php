<!-- Sidebar -->
<div class="default_sidebar">
    <div class="container mt-3">
        <a href="<?= Url() ?>"><button type="button" class="tt_block fullwidth p-2"><?= Txt("HOME") ?></button></a>
        <?php if ($_SESSION['class'] == _ADMINISTRATOR) : ?>
            <div class="dropdown">
                <button type="button" class="tt_block fullwidth p-2" data-bs-toggle="dropdown">
                    <?= Txt("ADMIN") ?>
                </button>
                <ul class="dropdown-menu">
                    <a href="<?= Url('adminbackup') ?>"><?= Txt("BACKUPS") ?></a><br>
                    <a href="<?= Url('adminblock') ?>"><?= Txt("BLOCKS") ?></a><br>
                    <a href="<?= Url('adminexceptions') ?>"><?= Txt("ERRORS") ?></a><br>
                    <a href="<?= Url('adminstylesheet') ?>"><?= Txt("THEME") ?></a><br>
                    <a href="<?= Url('adminconfig') ?>"><?= Txt("CONFIG") ?></a><br>
                    <a href="<?= Url('adminimport') ?>"><?= Txt("IMPORT") ?></a>
                </ul>
            </div>
        <?php endif; ?>
        <?php if ($_SESSION['class'] >= _SUPERMODERATOR) : ?>
            <div class="dropdown">
                <button type="button" class="tt_block fullwidth p-2" data-bs-toggle="dropdown">
                    <?= Txt("SUPER_MOD") ?>
                </button>
                <ul class="dropdown-menu">
                    <a href="<?= Url('admingroup') ?>"><?= Txt("GROUPS") ?></a><br>
                    <a href="<?= Url('adminshoutbox/clear') ?>"><?= Txt("CLEAR_SHOUTBOX") ?></a><br>
                    <a href="<?= Url('adminemail') ?>"><?= Txt("MASS_EMAIL") ?></a><br>
                    <a href="<?= Url('adminmessage') ?>"><?= Txt("MESSAGES") ?></a>
                </ul>
            </div>
        <?php endif; ?>
        <div class="dropdown">
            <button type="button" class="tt_block fullwidth p-2" data-bs-toggle="dropdown">
                <?= Txt("MODERATOR") ?>
            </button>
            <ul class="dropdown-menu">
                <a href="<?= Url('adminforumcat') ?>"><?= Txt("FORUM") ?></a><br>
                <a href="<?= Url('adminnews') ?>"><?= Txt("NEWS") ?></a><br>
                <a href="<?= Url('adminpoll') ?>"><?= Txt("POLLS") ?></a><br>
                <a href="<?= Url('adminuseradd') ?>"><?= Txt("ADD_USER") ?></a><br>
                <a href="<?= Url('adminusersearch') ?>"><?= Txt("USER_SEARCH") ?></a><br>
            </ul>
        </div>
        <div class="dropdown">
            <button type="button" class="tt_block fullwidth p-2" data-bs-toggle="dropdown">
                <?= Txt('TORRENT') ?>
            </button>
            <ul class="dropdown-menu">
                <a href="<?= Url('admincategorie') ?>"><?= Txt("TORRENT_CATEGORIES") ?></a><br>
                <a href="<?= Url('admintorrentlang') ?>"><?= Txt("LANGUAGE") ?></a><br>
                <a href="<?= Url('admintorrent') ?>"><?= Txt("TORRENT") ?></a><br>
                <a href="<?= Url('adminsnatch') ?>"><?= Txt("HNR") ?></a><br>
                <a href="<?= Url('adminpeer') ?>"><?= Txt("PEERS") ?></a><br>
                <a href="<?= Url('admintag') ?>"><?= Txt("TAGS") ?></a><br>
            </ul>
        </div>
        <div class="dropdown">
            <button type="button" class="tt_block fullwidth p-2" data-bs-toggle="dropdown">
                <?= Txt('CONNECTED') ?>
            </button>
            <ul class="dropdown-menu">
                <a href="<?= Url('adminprivacy') ?>"><?= Txt("PRIVACY") ?></a><br>
                <a href="<?= Url('adminban') ?>"><?= Txt("BANNED") ?></a><br>
                <a href="<?= Url('adminclient/peers') ?>"><?= Txt("CLIENT_BAN") ?></a><br>
                <a href="<?= Url('admincheat') ?>"><?= Txt("CHEATS") ?></a><br>
                <a href="<?= Url('adminduplicateip') ?>"><?= Txt("DUPLICATEIP") ?></a><br>
            </ul>
        </div>
        <div class="dropdown">
            <button type="button" class="tt_block fullwidth p-2" data-bs-toggle="dropdown">
                <?= Txt('ACTIVITY') ?>
            </button>
            <ul class="dropdown-menu">
                <a href="<?= Url('adminwarning') ?>"><?= Txt("WARNED_USERS") ?></a><br>
                <a href="<?= Url('adminreport') ?>"><?= Txt("REPORTS") ?></a><br>
                <a href="<?= Url('adminlog') ?>"><?= Txt("SITE_LOG"); ?></a><br>
                <a href="<?= Url('adminwhoswhere') ?>"><?= Txt("WHOS_WHERE") ?></a><br>
                <a href="<?= Url('admininactive') ?>"><?= Txt("INACTIVE") ?></a><br>
                <a href="<?= Url('admincomment') ?>"><?= Txt("COMMENT") ?></a><br>
                <a href="<?= Url('admininvite/pending') ?>"><?= Txt("PENDING_INVITE") ?></a><br>
                <a href="<?= Url('admininvite') ?>"><?= Txt("INVITED_USER") ?></a><br>
                <a href="<?= Url('gallery/avatar') ?>"><?= Txt("AVATAR_LOG") ?></a><br>
            </ul>
        </div>
    </div>
</div>
<!-- Sidebar -->