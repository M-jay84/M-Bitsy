<?php if ($USER->has('id') && $USER->has('control_panel') == "yes") :
    block_begin(Txt("STAFF")) ?>
    <select name="admin" style="width: 95%" onchange="if(this.options[this.selectedIndex].value != -1){ window.location = this.options[this.selectedIndex].value; }">
    <option value="-1"><?= Txt("NAVIGATION") ?></option>
    <option value="<?= Url('adminbackup') ?>"><?= Txt("BACKUPS") ?></option>
    <option value="<?= Url('adminban') ?>"><?= Txt("BANS") ?></option>
    <option value="<?= Url('adminblock') ?>"><?= Txt("BLOCKS") ?></option>
    <option value="<?= Url('admincheat') ?>"><?= Txt("CHEATS") ?></option>
    <option value="<?= Url('admincomment') ?>"><?= Txt("COMMENTS") ?></option>
    <option value="<?= Url('adminnews') ?>"><?= Txt("NEWS") ?></option>
    <option value="<?= Url('adminpeer') ?>"><?= Txt("PEERS") ?></option>
    <option value="<?= Url('adminpoll') ?>"><?= Txt("POLLS") ?></option>
    <option value="<?= Url('adminreport') ?>"><?= Txt("REPORTS") ?></option>
    <option value="<?= Url('adminlog') ?>"><?= Txt("LOG") ?></option>
    <option value="<?= Url('admincategorie') ?>"><?= Txt("CATEGORIES") ?></option>
    <option value="<?= Url('admintorrentlang') ?>"><?= Txt("LANGUAGE") ?></option>
    <option value="<?= Url('admintorrent') ?>"><?= Txt("TORRENTS") ?></option>
    <option value="<?= Url('admingroup') ?>"><?= Txt("GROUPS") ?></option>
    <option value="<?= Url('adminwarning') ?>"><?= Txt("WARNINGS") ?></option>
    <option value="<?= Url('adminwhoswhere') ?>"><?= Txt("WHOS_WHERE") ?></option>
    <option value="<?= Url('adminforumcat') ?>"><?= Txt("FORUM") ?></option>
    <option value="<?= Url('adminusersearch') ?>"><?= Txt("USER_SEARCH") ?></option>
    </select>
    <?php
    block_end();
endif;