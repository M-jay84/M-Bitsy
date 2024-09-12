<?php

use App\Models\TorrentLangModel;

 block_begin($page_title) ?>
<div class="text-center">
    <a href="<?= Url('browse') ?>"><?= LANG["BROWSE_TORRENTS"] ?></a>
</div>

<div class="tt_blockcenter rounded p-2">
    <form method="get" action="<?= Url("search/$V_url") ?>">
        <div class="input-group mb-3">
            <input type="text" name="keyword" placeholder="<?= LANG["SEARCH"] ?>" size="40" value="<?= stripslashes(htmlspecialchars($V_keyword)) ?>" />
            <span></span>
            <select name="cat" class='form-control'>
                <option value="0"><?php echo "(" . LANG["ALL"] . " " . LANG["TYPES"] . ")"; ?></option>
                <?php
                $catdropdown = "";
                foreach ($V_cats as $cat) {
                    $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
                    if ($cat["id"] == $_GET["cat"]) {
                        $catdropdown .= " selected=\"selected\"";
                    }
                    $catdropdown .= ">" . htmlspecialchars($cat["parent_cat"]) . ": " . htmlspecialchars($cat["name"]) . "</option>\n";
                }
                echo $catdropdown ?>
            </select>
        </div>
        <div class="input-group mb-3">
            <select name="incldead" class='form-control'>
                <option value="0"><?= LANG["ACTIVE_TRANSFERS"] ?></option>
                <option value="1">
                    <?php
                    if ($_GET["incldead"] == 1) {
                        echo "selected='selected'";
                    }
                    echo LANG["INC_DEAD"];
                    ?>
                </option>
                <option value="2">
                    <?php
                    if ($_GET["incldead"] == 2) {
                        echo "selected='selected'";
                    }
                    echo LANG["ONLY_DEAD"];
                    ?>
                </option>
            </select>
            <span></span>
            <select name="freeleech" class='form-control'>
                <option value="0"><?= LANG["ALL"] ?></option>
                <option value="1">
                    <?php
                    if ($_GET["freeleech"] == 1) {
                        echo "selected='selected'";
                    }
                    echo LANG["NOT_FREELEECH"];
                    ?>
                </option>
                <option value="2">
                    <?php
                    if ($_GET["freeleech"] == 2) {
                        echo "selected='selected'";
                    }
                    echo LANG["ONLY_FREELEECH"];
                    ?>
                </option>
            </select>
            <span></span>
            <?php
            if (CONFIG['ALLOWEXTERNAL']) { ?>
                <select name="inclexternal" class='form-control'>
                    <option value="0">
                        <?php echo LANG["LOCAL_EXTERNAL"]; ?>
                    </option>
                    <option value="1">
                        <?php
                        if ($_GET["inclexternal"] == 1) {
                            echo "selected='selected'";
                        }
                        echo LANG["LOCAL_ONLY"];
                        ?>
                    </option>
                    <option value="2">
                        <?php
                        if ($_GET["inclexternal"] == 2) {
                            echo "selected='selected'";
                        }
                        echo LANG["EXTERNAL_ONLY"];
                        ?>
                    </option>
                </select>
            <?php } ?>
            <span></span>
            <?= TorrentLangModel::langList($_GET['language']); ?><br><br>
        </div>
        <div class="text-center">
            <button type='submit' class='btn btn-sm ttbtn'><?= LANG["SEARCH"] ?></button>
        </div>
    </form>
</div>

<form id='sort' action=''>
    <div class='text-end'><?= LANG["SORT_BY"] ?>:
        <select name='sort' onchange='window.location="<?= $V_url ?>sort="+this.options[this.selectedIndex].value+"&amp;order="+document.forms["sort"].order.options[document.forms["sort"].order.selectedIndex].value'>
            <option value='id' <?php ($_GET["sort"] == "id" ? " selected='selected'" : "") ?>><?= LANG["ADDED"] ?></option>
            <option value='name' <?php ($_GET["sort"] == "name" ? " selected='selected'" : "") ?>><?= LANG["NAME"] ?></option>
            <option value='comments' <?php ($_GET["sort"] == "comments" ? " selected='selected'" : "") ?>><?= LANG["COMMENTS"] ?></option>
            <option value='size' <?php ($_GET["sort"] == "size" ? " selected='selected'" : "") ?>><?= LANG["SIZE"] ?></option>
            <option value='times_completed' <?php ($_GET["sort"] == "times_completed" ? " selected='selected'" : "") ?>><?= LANG["COMPLETED"] ?></option>
            <option value='seeders' <?php ($_GET["sort"] == "seeders" ? " selected='selected'" : "") ?>><?= LANG["SEEDERS"] ?></option>
            <option value='leechers' <?php ($_GET["sort"] == "leechers" ? " selected='selected'" : "") ?>><?= LANG["LEECHERS"] ?></option>
        </select>&nbsp;
        <select name='order' onchange='window.location="<?= $V_url ?>order="+this.options[this.selectedIndex].value+"&amp;sort="+document.forms["sort"].sort.options[document.forms["sort"].sort.selectedIndex].value'>
            <option selected='selected' value='asc' <?php ($_GET["order"] == "asc" ? " selected='selected'" : "") ?>><?= LANG["ASCEND"] ?></option>
            <option value='desc' <?php ($_GET["order"] == "desc" ? " selected='selected'" : "") ?>><?= LANG["DESCEND"] ?></option>
        </select>
    </div>
</form>

<?php torrenttable($V_torrents); ?>
<?= $V_pager ?>
<?php block_end(); ?>