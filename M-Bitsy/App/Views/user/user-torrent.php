<?php block_begin($page_title); ?>

<!-- Buttons -->
<p>
    <a href="<?= Url("download/$torrent[id]") ?>"><button type="button" class="btn btn-sm ttbtn"><i class='fa fa-download fa-1x' style='color:green'></i><?= LANG['DOWNLOAD'] ?></button></a>
    <?php if ($torrent['external'] == "yes") : ?>
        <button type="button" class="btn btn-sm ttbtn"><?= magnetlink($torrent) ?><?= LANG['MAGNET'] ?></button>
    <?php endif; ?>
    <span style="float:right;">
        <?php if ($current_user->id) : ?>
            <button type="button" class="btn btn-sm ttbtn"><a href='' data-bs-toggle="modal" data-bs-target="#reportModal"><?= LANG["REPORT"] ?></a></button>
        <?php endif; ?>
        <?php if ($torrent['seeders'] <= 1) : ?>
            <button type="button" class="btn btn-sm ttbtn"><a href='<?= Url("reseed/".$torrent['id']."") ?>'><?= LANG["RESEED"] ?></a></button>
        <?php endif; ?>
        <?php if ($current_user->edit_torrents == 'yes') : ?>
            <button type="button" class="btn btn-sm ttbtn"><a href="<?= Url("torrent/".$torrent['id']."/bump") ?>"><?= LANG["BUMP"] ?></a></button>
        <?php endif; ?>
    </span>
</p>

<!-- Main -->
<div class="row">
    <!-- Poster/Rating -->
    <div class="col-12 col-lg-3">
        <?= $image ?><br>
        <?= $html['ratings'] ?><br>
        <?= $thanks ?><br>
        <?= $likes['liked'] ?><br>
    </div>
    <!-- OMDB/TMDB/Description -->
    <div class="col-12 col-lg-5">
        <?php foreach ($tmdb1 as $row) : ?>
            <?= $row ?><br><br>
        <?php endforeach; ?>
    </div>
    <!-- Details 1 -->
    <div class="col-12 col-lg-2">
    <dl class="row">
        <dt class="col-sm-5"><?= LANG["SEEDERS"] ?></dt>
        <dd class="col-sm-7"><?= number_format($torrent['seeders']) ?></dd><hr>
        <dt class="col-sm-5"><?= LANG["LEECHERS"] ?></dt>
        <dd class="col-sm-7"><?= number_format($torrent['leechers']) ?></dd><hr>
        <dt class="col-sm-5"><?= LANG["HEALTH"] ?></dt>
        <dd class="col-sm-7"><img src='<?= Url("assets/images/health/health_" . health($torrent['leechers'], $torrent['seeders']) . ".webp") ?>' /></dd><hr>
        <dt class="col-sm-5"><?= LANG["ADDED"] ?></dt>
        <dd class="col-sm-7"><?php user_timezone_date($torrent['added']); ?></dd><hr>
        <dt class="col-sm-5"><?= LANG["UPLOADER"] ?></dt>
        <dd class="col-sm-7"><?= anon($torrent); ?></dd><hr>
        <dt class="col-sm-5"><?= LANG["VIEWS"] ?></dt>
        <dd class="col-sm-7"><?= number_format($torrent['views']) ?></dd><hr>
        <dt class="col-sm-5"><?= LANG["HITS"] ?></dt>
        <dd class="col-sm-7"><?= number_format($torrent['hits']) ?></dd><hr>
        <dt class="col-sm-5"><?= LANG["COMPLETED"] ?></dt>
        <dd class="col-sm-7"><?= number_format($torrent['times_completed']); ?>
        <?php if ($torrent['external'] != "yes" && $torrent['times_completed'] > 0) : ?>
            <a href='<?= Url("snatch/$torrent[id]/complete") ?>'><?php echo LANG["WHOS_COMPLETED"]; ?></a>]
        <?php endif; ?>
        </dd><hr>
        <?php if ($torrent['image1'] != "" || $torrent['image2'] != "") : ?>
            <dt class="col-sm-5"><?= LANG["IMAGES"] ?></dt>
            <dd class="col-sm-7"><a href='#' data-bs-toggle="modal" data-bs-target="#imagesModal"><i class="fa fa-picture-o fa-1x" title="<?= LANG["IMAGES"] ?>"></i></a></dd><hr>
        <?php endif; ?>
        <?php if (!empty($torrent['tube'])) : ?>
            <dt class="col-sm-5"><?= LANG["YOUTUBE"] ?></dt>
            <dd class="col-sm-7"><a href='#' data-bs-toggle="modal" data-bs-target="#youtubeModal"><i class="fa fa-film fa-1x" title="<?= LANG["YOUTUBE"] ?>"></i></a></dd><hr>
        <?php endif; ?>
    </dl>
    </div>
    <!-- Details 2 -->
    <div class="col-12 col-lg-2">
    <dl class="row">
        <dt class="col-sm-5"><?= LANG["CATEGORY"] ?></dt>
        <dd class="col-sm-7"><?= $torrent['cat_parent'] ?> / <?= $torrent['cat_name'] ?></dd><hr>
        <dt class="col-sm-5"><?= LANG["FILES"] ?></dt>
        <dd class="col-sm-7"><a href='#' data-bs-toggle="modal" data-bs-target="#filesModal"><i class="fa fa-file fa-1x" title="<?= LANG["Files"] ?>"></i></a></dd><hr>
        <dt class="col-sm-5"><?= LANG["COMMENTS"] ?></dt>
        <dd class="col-sm-7"><a href='<?= Url("comment/$torrent[id]/torrent") ?>'><?= LANG["COMMENTS"] ?></a></dd><hr>
        <dt class="col-sm-5"><?= LANG["LANG"] ?></dt>
        <dd class="col-sm-7"><?= $html['langname'] ?></dd><hr>
        <dt class="col-sm-5"><?= LANG["SIZE"] ?></dt>
        <dd class="col-sm-7"><?= mksize($torrent['size']) ?></dd><hr>
        <dt class="col-sm-5"><?= LANG["CHECKED"] ?></dt>
        <dd class="col-sm-7"><?php echo user_timezone_date($torrent['last_action']); ?></dd><hr>
        <dt class="col-sm-5"><a href='#' data-bs-toggle="modal" data-bs-target="#nfoModal"><?= LANG["NFO"] ?></a></dt>
        <dd class="col-sm-7"><?= $torrent['nfo'] ?></dd><hr>
        <dt class="col-sm-5"><?= LANG["TRACKERS"] ?></dt>
        <dd class="col-sm-7"><?= $html['externaltorrent'] ?></dd><hr>
        <?php if ($torrent['external'] != 'yes' && $torrent['freeleech'] == '1') : ?>
            <dt class="col-sm-5"><?= LANG["FREE_LEECH"] ?></dt>
            <dd class="col-sm-7"><?= LANG["YES"] ?></dd><hr>
        <?php endif; ?>
        <?php if ($torrent['vip'] == 'yes') : ?>
            <dt class="col-sm-5"><?= LANG["VIP"] ?></dt>
            <dd class="col-sm-7"><?= LANG["YES"] ?></dd><hr>
        <?php endif; ?>
        <?php if (CONFIG['ALLOWLIKES'] && $current_user->id) : ?>
            <dt class="col-sm-5"><?= LANG['LIKES'] ?></dt>
            <dd class="col-sm-7"><?= $likes['like'] ?></dd><hr>
        <?php endif; ?>
    </dl>
    </div>

    <!-- Admin/Owner Buttons -->
    <div>
    <?php if ($torrent['external'] == 'yes') : ?>
        <?= $scraper ?>
    <?php endif; ?>
    <span style="float:right;">
        <?php if ($current_user->edit_torrents == 'yes') : ?>
            <a href="<?= Url("peer/torrent/$torrent[id]") ?>"><i class="fa fa-users fa-2x" title="<?= LANG["PEERS"] ?>"></i></a>&nbsp;
            <a href="<?= Url("snatch/$torrent[id]/torrent") ?>"><i class="fa fa-history fa-2x" title='<?= LANG["SNATCHLIST"] ?>'></i></a>&nbsp;
        <?php endif; ?>
        <?php if ($current_user->id == $torrent['id'] || $current_user->edit_torrents == 'yes') : ?>
            <a href='' data-bs-toggle="modal" data-bs-target="#editModal"><i class="fa fa-pencil fa-2x" title="<?= LANG["EDIT_TORRENT"] ?>"></i></a>
        <?php endif; ?>
        <?php if ($current_user->delete_torrents == 'yes') : ?>
            <a href='' data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="fa fa-trash fa-2x" title="<?= LANG["DEL_TORRENT"] ?>"></i></a>
        <?php endif; ?>
    </span>
    </div>
</div>

<?php block_end(); ?>

<!-- Image Modal -->
<div class="modal fade" id="imagesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" style="min-width:800px;max-height:800px;">
        <div class="modal-content">
            <div class="modal-body">
                <?php if ($torrent['image1'] != "") : ?>
                    <img src='<?= data_uri(UPLOAD_PATH . "/images/" .$torrent['image1'], $torrent['image1']) ?>' height='250' width='250' />&nbsp;
                <?php endif; ?>
                <?php if ($torrent['image2'] != "") : ?>
                    <img src='<?= data_uri(UPLOAD_PATH . "/images/" .$torrent['image2'], $torrent['image2']) ?>' height='250' width='250' />
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Youtube Modal -->
<div class="modal fade" id="youtubeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" style="min-width:800px;max-height:800px;">
        <div class="modal-content">
            <div class="modal-body">
                <embed src='<?php echo str_replace("watch?v=", "v/", htmlspecialchars($torrent['tube'])) ?>' type="application/x-shockwave-flash"  height="410" width='100%'></embed>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
            </div>
        </div>
    </div>
</div>

<!-- File Modal -->
<div class="modal fade" id="filesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center">
                    <h3><?php echo LANG["FILE_LIST"] ?></h3>
                </div> 
                <div class=" rounded p-2">
                    <?php
                    $files = unserialize($torrent['filelist']);
                    foreach ($files as $key1 => $value1) {
                        $size = $value1 == is_int($value1) ? mksize($value1) : '';
                        echo $key1 . '&nbsp;' . $size . '<br>';
                        foreach ($value1 as $key2 => $value2) {
                            $size = $value2 == is_int($value2) ? mksize($value2) : '';
                            echo '&nbsp;&nbsp;&nbsp;-&nbsp;' . $key2 . '&nbsp;' . $size . '<br>';
                            foreach ($value2 as $key3 => $value3) {
                                $size = $value3 == is_int($value3) ? mksize($value3) : '';
                                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;' . $key3 . '&nbsp;' . $size . '<br>';
                                foreach ($value3 as $key4 => $value4) {
                                    $size = $value4 == is_int($value4) ? mksize($value4) : '';
                                    echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;' . $key4 . '&nbsp;' . $size . '<br>';
                                }
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- NFO Modal -->
<div class="modal fade" id="nfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" style="min-width:800px;max-height:800px;">
        <div class="modal-content">
            <div class="modal-body">
                <?php displaynfo($torrent['id']) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Report Modal -->
<form id="submit" method="post" action="<?= Url('report') ?>">
    <div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-body">
                    <p class="text-center"><?= LANG['ARE_YOU_SURE_REPORT'] ?> torrent.</p>
                    <input type='hidden' name='id' id='id' value='<?= $torrent['id'] ?>' />
                    <input type='hidden' name='type' id='type' value='torrent' />
                    <input class="form-control fullwidth" type='text' name='reason' placeholder="Enter Reason"><br>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                    <button type="submit" class="btn btn-sm ttbtn"><?= LANG["SAVE"] ?></button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Tracker Modal -->
<div class="modal fade" id="trackerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
        <div class="modal-content">
            <div class="modal-body">
                <?php if ($torrent['external'] == 'yes') : ?>
                    <div class='text-center  rounded p-4'><h3>Trackers</h3><?= $torrent['announce'] ?><br>
                        <?php
                        $array = unserialize($torrent['announcelist']);
                        foreach ($array as $subarray) {
                            echo $subarray . "<br>";
                        }
                        ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                <button type="submit" class="btn btn-sm ttbtn"><?= LANG["SAVE"] ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<?php if ($current_user->delete_torrents == 'yes' ||  $current_user->id ==  $torrent['owner']) : ?>
    <form id="delsubmit" method="post" action="<?= Url('torrent/deleteok') ?>">
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
                <div class="modal-content">
                    <div class="modal-body">
                        <input type="hidden" name="id" value="<?= $torrent['id'] ?>" />
                        <div class="text-center">
                            <input type='hidden' name='id' value='<?= $torrent['id'] ?>' />
                            <input type='hidden' name='torrentname' value='<?= htmlspecialchars($torrent['name']); ?>' />
                            <b><?= LANG["REASON_FOR_DELETE"]; ?></b><input type='text' size='30' name='delreason' />
                            &nbsp;<input type='submit' class="btn btn-sm ttbtn" value='<?= LANG["DELETE_TORRENT"]; ?>' />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                        <button type="submit" class="btn btn-sm ttbtn"><?= LANG["SAVE"] ?></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php endif; ?>

<!-- Edit Modal -->
<?php if ($current_user->edit_torrents == 'yes' || $current_user->id == $torrent['owner']) : ?>
    <form id="submit" method="post" action="<?= Url("torrent/submit/$torrent[id]") ?>" enctype="multipart/form-data">
        <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:1200px;">
                <div class="modal-content">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                        <button type="submit" class="btn btn-sm ttbtn"><?= LANG["SAVE"] ?></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" value="<?= $torrent['id'] ?>" />
                        <div class="row">
                            <div class="col">
                                <b><?= LANG["NAME"] ?>: </b><br>
                                <input class="form-control" type="text" name="name" value="<?= htmlspecialchars($torrent['name']) ?>" size="60" /><br><br>
                                <b><?= LANG["IMAGE"] ?> 1:</b><br>
                                <input type='radio' name='img1action' value='keep' checked='checked' /><?= LANG["KEEP_IMAGE"] ?>&nbsp;&nbsp;
                                <input type='radio' name='img1action' value='delete' /><?= LANG["DELETE_IMAGE"] ?>&nbsp;&nbsp;
                                <input type='radio' name='img1action' value='update' /><?= LANG["UPDATE_IMAGE"] ?><br />
                                <input type='file' name='image0' size='60' /> <br /><br /> <b><?= LANG["IMAGE"] ?> 2:</b>&nbsp;&nbsp;<br>
                                <input type='radio' name='img2action' value='keep' checked='checked' /><?= LANG["KEEP_IMAGE"] ?>&nbsp;&nbsp;
                                <input type='radio' name='img2action' value='delete' /><?= LANG["DELETE_IMAGE"] ?>&nbsp;&nbsp;
                                <input type='radio' name='img2action' value='update' /><?= LANG["UPDATE_IMAGE"] ?><br />
                                <input type='file' name='image1' size='60' /><br><br>
                                <b><?= LANG["NFO"] ?>: </b><br />
                                <input type='radio' name='nfoaction' value='keep' checked='checked' />Keep NFO &nbsp;
                                <input type='radio' name='nfoaction' value='delete' />Delete NFO &nbsp;
                                <input type='radio' name='nfoaction' value='update' />Update NFO:
                                <input type='file' name='nfofile' size='60'><br><br>
                                <b><?= LANG["CATEGORIES"]  ?>: </b><br>
                                <select name="type" class='form-select'>
                                    <?= $html['catdrop']  ?>
                                </select>
                                <br><br>
                                <b><?= LANG["Tags"]; ?>: </b><?= $tags ?><br>
                                <select class="form-select" id="validationTags" name="tags[]" multiple data-allow-clear="true" data-suggestions-threshold="0">
                                    <option selected disabled hidden value="">Add All Tags</option>
                                    <?php foreach ($html['taglist'] as $row) :
                                        echo "<option value=" . $row['name'] . ">" . $row['name'] . "</option>";
                                    endforeach; ?>
                                </select><br>
                                <b><?= LANG["LANG"]  ?>: </b><br>
                                <?= $html['lang'] ?><br><br>
                            </div>
                            <div class="col">
                                <b><?= LANG["TMDB"] ?></b><br>
                                <input class="form-control" type="text" name="tmdb" value="<?= htmlspecialchars($torrent['tmdb'])  ?>" size="60"><br><br>
                                <b><?= LANG["IMDB"] ?></b><br>
                                <input class="form-control" type="text" name="imdb" value="<?= htmlspecialchars($torrent['imdb'])  ?>" size="60"><br><br>
                                <?php if (CONFIG['YOU_TUBE']) : ?>
                                    <b><?= LANG["VIDEOTUBE"] ?>: </b><br>
                                    <input class="form-control" type='text' name='tube' value='<?= htmlspecialchars($torrent['tube']) ?>' size='60' /><br>&nbsp;<i><?= LANG["FORMAT"] ?>: </i> <span style='color:#FF0000'><b>https://www.youtube.com/watch?v=aYzVrjB-CWs</b></span><br><br>
                                <?php endif; ?>
                                <?php if ($current_user->edit_torrents == 'yes') : ?>
                                    <b><?= LANG["BANNED"]  ?>: </b><br>
                                    <input type='checkbox' name='banned' <?= (($torrent['banned'] == "yes") ? " checked='checked'" : "") ?> value='yes'> <?= LANG["BANNED"] ?><br /><br>
                                    <?php endif; ?>
                                <?php if ($current_user->class >= 5) : ?>
                                    <b><?= LANG["STICKY"] ?>: </b><br>
                                    <input type="checkbox" name="sticky" <?= (($torrent['sticky'] == "yes") ? " checked='checked'" : "")  ?> value="yes" />Set sticky this torrent!<br><br>
                                <?php endif; ?>
                                <b><?= LANG["VISIBLE"]  ?>: </b><br>
                                <input type="checkbox" name="visible" <?= (($torrent['visible'] == "yes") ? " checked='checked'" : "") ?> value="yes"> <?= LANG["VISIBLEONMAIN"] ?><br /><br>
                                <?php if ($torrent['external'] != "yes" && $current_user->edit_torrents == 'yes') : ?>
                                    <b><?= LANG["FREE_LEECH"] ?>: </b><br>
                                    <input type="checkbox" name="freeleech" <?= (($torrent['freeleech'] == "1") ? " checked=\"checked\"" : "") ?> value="1"><?= LANG["FREE_LEECH_MSG"] ?><br /><br>
                                <?php endif; ?>
                                <?php if ($torrent['external'] != "yes" && $current_user->edit_torrents == 'yes') : ?>
                                    <b>VIP:</b><br>
                                    <input name=vip type='checkbox' <?= (($torrent['vip'] == "yes") ? " checked='checked'" : "")  ?> value='yes' /> Check the box to make the torrent VIP only.<br>
                                <?php endif; ?>
                                <?php if (CONFIG['ANONYMOUSUPLOAD']) : ?>
                                    <b><?= LANG["ANONYMOUS_UPLOAD"]  ?>: </b><br>
                                    <input type="checkbox" name="anon" <?= (($torrent['anon'] == "yes") ? " checked=\"checked\"" : "")  ?> value="1" /><br>(<?= LANG["ANONYMOUS_UPLOAD_MSG"] ?>)<br />
                                <?php endif; ?>
                            </div>
                        </div><br>
                        <center><b><?= LANG["DESCRIPTION"]; ?>:</b></center><br>
                        <?php print textbbcode("bbform", "descr", htmlspecialchars($torrent['descr'])); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                        <button type="submit" class="btn btn-sm ttbtn"><?= LANG["SAVE"] ?></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php endif; ?>

<?php
/* Simil torrent
$res = TorrentsLogic::getAll("WHERE visible = ? AND banned = ? AND torrents.category = ? ORDER BY sticky, added DESC, id DESC", ['yes', 'no', $category]);
if ($re['count'] > 0) {
    echo '<br><center><b>Similar Torrents</b></center>';
    torrenttable($res['stmt']);
} else {
    echo '<br><center><b>No Similar Torrents Found</b></center><br>';
}
*/