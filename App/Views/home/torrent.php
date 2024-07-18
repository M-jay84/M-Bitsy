<?php

use App\Models\TorrentsModel;
use App\Models\UsersModel;

block_begin(Txt('TORRENT'));
if (CONFIG['MEMBERSONLY'] && !$data['USER']->has('id')) {
    echo Txt('MEMBERS_ONLY');  
} else {
    $res = TorrentsModel::getAll("WHERE visible = ? AND banned = ?", ['yes', 'no']); 
    if ($res['count'] > 0) {
        ?>
        <div class="text-center">
            <a href='<?= Url('browse') ?>'><?= Txt("BROWSE_TORRENTS") ?></a> - 
            <a href='<?= Url('search') ?>'><?= Txt("SEARCH_TORRENTS") ?></a>
        </div>
        <?php
        torrenttable($data['USER'], $res['stmt']);
    } else {
        echo Txt('NO_TORRENT_FOUND');
    }
    if ($data['USER']->has('id')) {
        UsersModel::update(['last_browse'], ['id='], [gmtime(),$data['USER']->has('id')]);
    }
}
block_end();