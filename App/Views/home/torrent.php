<?php

use App\Models\TorrentsModel;
use App\Libs\DB;

block_begin(LANG['TORRENT']);
if (CONFIG['MEMBERSONLY'] && !$current_user->id) {
    echo LANG['MEMBERS_ONLY'];  
} else {
    $res = TorrentsModel::getAll("WHERE visible = ? AND banned = ?", ['yes', 'no']); 
    if ($res['count'] > 0) {
        ?>
        <div class="text-center">
            <a href='<?= Url('browse') ?>'><?= LANG["BROWSE_TORRENTS"] ?></a> - 
            <a href='<?= Url('search') ?>'><?= LANG["SEARCH_TORRENTS"] ?></a>
        </div>
        <?php
        torrenttable($res['stmt']);
    } else {
        echo LANG['NO_TORRENT_FOUND'];
    }
    if ($current_user->id) {
        DB::update('users', ['last_browse'], ['id='], [gmtime(),$current_user->id]);
    }
}
block_end();