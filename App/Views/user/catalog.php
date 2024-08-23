<?php use App\Libs\MDBS; ?>
<div class="tt_block rounded">
    <div class="tt_blockhead text-center">
        <?= $page_title ?>
    </div>
    <div class="p-3 post-body">
        <div class="text-center">
            <form method=get action=?>
                <input type=text name=search placeholder='<?= LANG["SEARCH_CAT"] ?>' id=searchinput>
                <input type=submit class='btn btn-sm ttbtn' value="<?= LANG["SEARCH"] ?>">
            </form>
        </div>
        <?php letters(); ?>

        <div class="row">
            <?= $V_pager ?>
            <?php foreach ($V_torrents as $row) :
                $shortname = CutName($row['name'], 40);
                if (($row["anon"] == "yes" || $cat["privacy"] == "strong") && $current_user->id != $row["owner"] && !$current_user->edit_torrents == 'yes') {
                    $owner2 = "<b>" . LANG["ADDED_BY"] . ": </b><i>" . LANG["ANONYMOUS"] . "</i>";
                } else {
                    $owner2 = "<b>" . LANG["ADDED_BY"] . ": </b>" . coloredname($row['owner']) . "";
                }
                $row["lang_name"] = empty($row["lang_name"]) ? "Unknown/NA" : $row["lang_name"];
                $lang = "<b>" . LANG["LANG"] . " : </b>" . $row["lang_name"] . "";
                ?>
                <div class="col-sm-4 m-1 post-message" style='width: 32.33333333%;'>
                    <div class="row rounded">
                        <div class="col-12 text-center">
                            <a href="<?= Url("torrent/$row[id]/hit") ?>"><b><?= $shortname ?></b></a>
                        </div>
                        <div class="col-6">
                            <img src="<?= MDBS::getImage($row); ?>" width="100%" height="250" title="<?= $shortname; ?>">
                        </div>
                        <div class="col-6">
                            <b><?= $owner2 ?></b><br>
                            <b><?= LANG["CATEGORY"] ?> : </b><?= $row["cat_parent"] ?><br>
                            <?= $lang ?><br>
                            <b><?= LANG["SIZE"] ?> : </b><?= mksize($row["size"]) ?><br>
                            <b><?= LANG["LEECHERS"] ?></b>&nbsp;<?= $row["leechers"] ?><br>
                            <b><?= LANG["SEEDERS"] ?></b>&nbsp;<?= $row["seeders"] ?><br>
                            <b><?= LANG["Completed"] ?></b>&nbsp;<?= $row['times_completed'] ?><br>
                            <a href="<?= Url("downloadload/$row[id]") ?>"><?= LANG["DOWNLOAD"] ?></a>
                        </div>
                        <div class="col-12">
                            <?php $shortdescription = mb_strimwidth($row['descr'], 0, 10, "..."); ?>
                            <div class="text-center">
                                <?= LANG["DESCRIPTION"] ?><br>
                                <i><b>
                                        <font size=2><?php echo format_comment($shortdescription) ?></font>
                                    </b></i>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>