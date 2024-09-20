<?php

use App\Libs\DB;
use App\Models\TorrentLangModel;

block_begin($TITLE) ?>
<div class="tt_blockcenter rounded">
    <form method="get" action="<?= Url("browse/$URL") ?>">
        <p class="text-center">
            <input type="text" name="keyword" placeholder="<?= LANG["SEARCH"] ?>" size="40" value="<?= stripslashes(htmlspecialchars($KEYWORD)) ?>" /><br>
            <?php
            $i = 0;
            foreach ($CATS as $cat) {
                $catsperrow = 5;
                print(($i && $i % $catsperrow == 0) ? "<br><br>" : "");
                $get_cat = isset($_GET["cat"]) ? $_GET["cat"] : '';
                print("<a href=" . Url("browse?cat=$cat[id]") . ">" . htmlspecialchars($cat["parent_cat"]) . " - " . htmlspecialchars($cat["name"]) . "</a> <input name='c{$cat["id"]}' type=\"checkbox\" " . (in_array($cat["id"], $WHERE_CAT_IN) || $get_cat == $cat["id"] ? "checked='checked' " : "") . "value='1' />&nbsp;\n");
                $i++;
            }
            echo "<br><br><a href='" . Url('browse') . "' type='submit' class='ttbtn'>" . LANG["RESET"] . "</a>&nbsp;<input type='submit' class='ttbtn' value='" . LANG["GO"] . "' />";
            echo "</p></div>";
            //if we are browsing, display all subcats that are in same cat
            if ($PARENT_CAT) {
                $URL .= "parent_cat=" . urlencode($PARENT_CAT) . "&amp;";
                echo "<br /><br /><b>" . LANG["YOU_ARE_IN"] . ":</b> <a href='" . Url("browse?parent_cat=" . urlencode($PARENT_CAT) . "") . "'>" . htmlspecialchars($PARENT_CAT) . "</a><br /><b>" . LANG["SUB_CATS"] . ":</b> ";
                $subcatsquery = DB::fetchAll('categories', 'id, name, parent_cat', ['parent_cat='], [$PARENT_CAT], 'ORDER BY name');
                foreach ($subcatsquery as $subcatsrow) {
                    $name = $subcatsrow['name'];
                    echo " - <a href=" . Url("browse?cat=$subcatsrow[id]") . ">$name</a>";
                }
            }

            if (intval($PAGER)) {
                $URL .= "page=$_GET[page]&amp;";
            }

            echo "</center><br /><br />"; //some spacing

            ?>

        <div class="tt_blockcenter rounded p-2">
            <div class='text-center'><a data-bs-toggle='collapse' data-bs-target='#collapseExample' aria-expanded='false' aria-controls='collapseExample'>More Search</a></div>
            <div class="collapse" id="collapseExample">

                <div class="input-group mb-3">
                    <span></span>
                    <select name="cat" class='form-control'>
                        <option value="0"><?php echo "(" . LANG["ALL"] . " " . LANG["TYPES"] . ")"; ?></option>
                        <?php
                        $catdropdown = "";
                        foreach ($CATS as $cat) {
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

<?php torrenttable($TORRENTS); ?>
<?= $PAGER ?>
<?php block_end(); ?>