<?php if ($_SESSION['id'] && $_SESSION['control_panel'] == "yes") :
    block_begin(LANG["STAFF"]) ?>
    <select name="admin" style="width: 95%" onchange="if(this.options[this.selectedIndex].value != -1){ window.location = this.options[this.selectedIndex].value; }">
    <option value="-1"><?= LANG["NAVIGATION"] ?></option>
    <option value="<?= Url('adminbackup') ?>"><?= LANG["BACKUPS"] ?></option>
    <option value="<?= Url('adminban') ?>"><?= LANG["BANS"] ?></option>
    <option value="<?= Url('adminblock') ?>"><?= LANG["BLOCKS"] ?></option>
    <option value="<?= Url('admincheat') ?>"><?= LANG["CHEATS"] ?></option>
    <option value="<?= Url('admincomment') ?>"><?= LANG["COMMENTS"] ?></option>
    <option value="<?= Url('adminnews') ?>"><?= LANG["NEWS"] ?></option>
    <option value="<?= Url('adminpeer') ?>"><?= LANG["PEERS"] ?></option>
    <option value="<?= Url('adminpoll') ?>"><?= LANG["POLLS"] ?></option>
    <option value="<?= Url('adminreport') ?>"><?= LANG["REPORTS"] ?></option>
    <option value="<?= Url('adminlog') ?>"><?= LANG["LOG"] ?></option>
    <option value="<?= Url('admincategorie') ?>"><?= LANG["CATEGORIES"] ?></option>
    <option value="<?= Url('admintorrentlang') ?>"><?= LANG["LANGUAGE"] ?></option>
    <option value="<?= Url('admintorrent') ?>"><?= LANG["TORRENTS"] ?></option>
    <option value="<?= Url('admingroup') ?>"><?= LANG["GROUPS"] ?></option>
    <option value="<?= Url('adminwarning') ?>"><?= LANG["WARNINGS"] ?></option>
    <option value="<?= Url('adminwhoswhere') ?>"><?= LANG["WHOS_WHERE"] ?></option>
    <option value="<?= Url('adminforumcat') ?>"><?= LANG["FORUM"] ?></option>
    <option value="<?= Url('adminusersearch') ?>"><?= LANG["USER_SEARCH"] ?></option>
    </select>
    <?php
    block_end();
endif;