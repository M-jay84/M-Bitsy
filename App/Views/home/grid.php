<?php
use App\Libs\MDBS;
use App\Models\TorrentsModel;
if ($current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
    $stmt = TorrentsModel::getAll("WHERE visible = ? AND banned = ?", ['yes', 'no']);
    block_begin(LANG['GRID']);
    ?>
    <div class="row"> <?php
        foreach ($stmt['stmt'] as $row) { ?>
            <div class="col-2">
                <a href='<?= Url("torrent/$row[id]") ?>'>

                    <img src="<?= MDBS::getImage($row) ?>" width="200" height="350">
                </a><br>
                <p class="text-center"> <?php
                    echo CutName($row['name'], 20); ?><br>
                    <font color="#00cc00"><b>S</b></font> <?= $row['seeders']; ?><font color="#FF0000"><b> L </b></font><?= $row['leechers']; ?>
                </p>
            </div> <?php
        } ?>
    </div>
    <?php
    block_end();
}