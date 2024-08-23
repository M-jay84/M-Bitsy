<?php
use App\Libs\MDBS;
use App\Models\TorrentsModel;
if ($current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
    $stmt = TorrentsModel::getAll("WHERE visible = ? AND banned = ?", ['yes', 'no']);
    block_begin(LANG['CAROUSEL']); ?>
    <div id="recipeCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner" role="listbox"> <?php
            $i = 0;
            foreach ($stmt['stmt'] as $row) {
                if ($i == 0) {
                    $set_ = 'active';
                } else {
                    $set_ = '';
                } ?>
                <div class='carousel-item <?= $set_; ?>'>
                    <div class="col-md-1">
                        <div class='scaleA'>
                            <a href='<?= Url("torrent/$row[id]") ?>'><img src="<?php echo MDBS::getImage($row); ?>" width="100" height="250" title="<?= $row['name'] ?>"></a>
                            <div class='text-center'>
                                <font color="#00cc00"><b>S</b></font> <?= $row['seeders'] ?><font color="#FF0000"><b> L </b></font><?= $row['leechers'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php $i++;
            } ?>
        </div>
        <a class="carousel-control-prev bg-transparent w-aut" href="#recipeCarousel" role="button" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </a>
        <a class="carousel-control-next bg-transparent w-aut" href="#recipeCarousel" role="button" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </a>
    </div>
<?php block_end() ?>
<?php } ?>