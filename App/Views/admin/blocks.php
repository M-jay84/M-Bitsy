<?php block_begin($page_title) ?>
<p class="text-center">
    Place new blocks in App/Views/blocks
    <a type="button" class="ttbtn btn-sm" data-bs-toggle="modal" data-bs-target="#addblockModal"><?= LANG["ADD"] ?></a>
</p>
<form name="blocks" method="post" action="<?= Url('adminportal/true') ?>">
    <div class='table-responsive  p-3'>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th><?= LANG["NAME"] ?><br />(<?= LANG["_FL_NM_IF_NO_SET_"] ?>)</th>
                    <th><?= LANG["_FILE_NAME_"] ?><br></th>
                    <th><?= LANG["DESCRIPTION"] ?><br />(<?= LANG["_MAX_"] ?> 255 <?= LANG["_CHARS_"] ?>)</th>
                    <th><?= LANG["POSITION"] ?></th>
                    <th><?= LANG["SORT"] ?></th>
                    <th><?= LANG["ORDER"] ?></th>
                    <th><?= LANG["ENABLED"] ?><br><?= LANG["YES"] ?>/<?= LANG["NO"] ?></th>
                </tr>
            </thead>
            <?php foreach ($V_blocks as $row) : ?>
                <?php
                
                switch ($row["position"]) {
                    case "left":
                        $pos = LANG["LEFT"];
                        break;
                    case "middle":
                        $pos = LANG["MIDDLE"];
                        break;
                    case "right":
                        $pos = LANG["RIGHT"];
                        break;
                }
                ?>
                <tr>
                    <td> <!-- Name -->
                        <input type="text" name="named_<?= $row["id"] ?>" value="<?php echo ($row["named"] ? $row["named"] : $row["name"]) ?>" />
                    </td>
                    <td> <!-- File Name -->
                        <?= $row["name"] ?>
                    </td>
                    <td> <!-- Description -->
                        <textarea name="description_<?= $row["id"] ?>" rows="2" cols="20"><?= $row["description"] ?></textarea>
                    </td>
                    <td> <!-- Position -->
                        <?= $pos ?>
                    </td>
                    <td> <!-- Sort -->
                        <?php echo $row["sort"] ?>
                    </td>
                    <td> <!-- Edit Sort (Order) -->
                        <?php echo ((($row["position"] != "left") && ($row["enabled"] == 1)) ? "<a href=\"" . Url("adminportal/true&position=left&left=$row[id]") . "\"><img border=\"0\" src=\"" . CONFIG['URLROOT'] . "/assets/images/blocks/leftenable.webp\" width=\"18\" height=\"15\"  /></a>" : "<img border=\"0\" src=\"" . CONFIG['URLROOT'] . "/assets/images/blocks/leftdisable.webp\" width=\"18\" height=\"15\" " . ($row["enabled"] ? "alt=\"" . LANG["_AT_LEFT_"] . "\"" : "") . " " . ($row["enabled"] ? "onclick=\"javascript: alert('" . LANG["_AT_LEFT_"] . "');\"" : "onclick=\"javascript: alert('" . LANG["_MUST_ENB_FIRST"] . "');\"") . "  />") ?>
                        <?php echo ((($row["position"] != "middle") && ($row["enabled"] == 1)) ? "<a href=\"" . Url("adminportal/true&position=middle&middle=$row[id]") . "\"><img border=\"0\" src=\"" . CONFIG['URLROOT'] . "/assets/images/blocks/middleenable.webp\" width=\"18\" height=\"15\" /></a>" : "<img border=\"0\" src=\"" . CONFIG['URLROOT'] . "/assets/images/blocks/middledisable.webp\" width=\"18\" height=\"15\" " . ($row["enabled"] ? "alt=\"" . LANG["_AT_CENTER_"] . "\"" : "") . " " . ($row["enabled"] ? "onclick=\"javascript: alert('" . LANG["_AT_CENTER_"] . "');\"" : "onclick=\"javascript: alert('" . LANG["_MUST_ENB_FIRST"] . "');\"") . "  />") ?>
                        <?php echo ((($row["position"] != "right") && ($row["enabled"] == 1)) ? "<a href=\"" . Url("adminportal/true&position=right&right=$row[id]") . "\"><img border=\"0\" src=\"" . CONFIG['URLROOT'] . "/assets/images/blocks/rightenable.webp\" width=\"18\" height=\"15\" /></a>" : "<img border=\"0\" src=\"" . CONFIG['URLROOT'] . "/assets/images/blocks/rightdisable.webp\" width=\"18\" height=\"15\" " . ($row["enabled"] ? "alt=\"" . LANG["_AT_RIGHT_"] . "\"" : "") . " " . ($row["enabled"] ? "onclick=\"javascript: alert('" . LANG["_AT_RIGHT_"] . "');\"" : "onclick=\"javascript: alert('" . LANG["_MUST_ENB_FIRST"] . "');\"") . "  />") ?>
                        <?php echo ((($row["sort"] != 1) && ($row["enabled"] != 0)) ? "<a href=\"" . Url("adminportal/true&position=$row[position]&sort=$row[sort]&up=$row[id]") . "\"><img border=\"0\" src=\"" . CONFIG['URLROOT'] . "/assets/images/blocks/updisable.webp\" width=\"18\" height=\"15\" /></a>" : "<img border='0' src=\"" . CONFIG['URLROOT'] . "/assets/images/blocks/updisable.webp\" width=\"18\" height=\"15\" alt=\"" . ($row["enabled"] ? "" . LANG["_AT_TOP_"] . "" : "") . "\" " . ($row["enabled"] ? "onclick=\"javascript: alert('" . LANG["_AT_TOP_"] . "');\"" : "onclick=\"javascript: alert('" . LANG["_MUST_ENB_FIRST"] . "');\"") . " />") ?>
                        <?php echo (((($row["sort"] != ($nextleft - 1)) && ($row["position"] == "left") || ($row["sort"] != ($nextright - 1)) && ($row["position"] == "right") || ($row["sort"] != ($nextmiddle - 1)) && ($row["position"] == "middle")) && ($row["enabled"] != 0)) ? "<a href=\"" . Url("adminportal/true&position=$row[position]&sort=$row[sort]&down=$row[id]") . "\"><img border=\"0\" src=\"" . CONFIG['URLROOT'] . "/assets/images/blocks/downenable.webp\" width=\"18\" height=\"15\" /></a>" : "<img border=\"0\" src=\"" . CONFIG['URLROOT'] . "/assets/images/blocks/downdisable.webp\" width=\"18\" height=\"15\" alt=\"" . ($row["enabled"] ? "" . LANG["_AT_BOTTOM_"] . "" : "") . "\" " . ($row["enabled"] ? "onclick=\"javascript: alert('" . LANG["_AT_BOTTOM_"] . "');\"" : "onclick=\"javascript: alert('" . LANG["_MUST_ENB_FIRST"] . "');\"") . " />") ?>
                    </td>
                    <td> <!-- Enable -->
                        <?php echo "
                        <input type=\"radio\" name=\"enable_" . $row["id"] . "\"" . ($row["enabled"] ? " checked=\"checked\"" : "") . " value=\"1\" />
                        <input type=\"radio\" name=\"enable_" . $row["id"] . "\"" . (!$row["enabled"] ? " checked=\"checked\"" : "") . " value=\"0\" />
                        </td>"; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <p class="text-center">
        <button type="submit" class="btn btn-sm ttbtn"><?= LANG['SUBMIT'] ?></button>
    </p>
</form>

<!-- Add Modal -->
<form id="addnewblock" name="addblockModal" method="post" action="<?= Url('adminportal/submit') ?>">
    <div class="modal fade" id="addblockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-body">
                    <table class='table table-striped'>
                        <thead>
                            <tr>
                                <th><?= LANG["_NAMED_"] ?> (<?= LANG["_FL_NM_IF_NO_SET_"] ?>)</th>
                                <th><?= LANG["FILE"] ?></th>
                                <th><?= LANG["DESCRIPTION"] ?> (<?= LANG["_MAX_"] ?> 255 <?= LANG["CHARS"] ?>)</th>
                                <th><?= LANG["ADD"] ?></th>
                                <th><?= LANG["DEL"] ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 0; ?>
                            <?php foreach ($V_files as $row) : ?>
                                <tr>
                                    <td>
                                        <input type="hidden" name="addblock_<?= $i ?>" value="<?= $row ?>" />
                                        <input type="text" name="wantedname_<?= $i ?>" value="<?php echo str_replace("_block.php", "", $row) ?>" />
                                    </td>
                                    <td><?= $row; ?></td>
                                    <td><textarea name="wanteddescription_<?= $i ?>" rows='2' cols='20'></textarea></td>
                                    <td>
                                        <div id="addn_<?= $i ?>">
                                            <input type='checkbox' name='addnew[]' value="<?= $i ?>" onclick="javascript: if(dltp_<?php echo $i ?>.style.display=='none'){dltp_<?php echo $i ?>.style.display='block'}else{dltp_<?php echo $i ?>.style.display='none'}; \" />
                                        </div>
                                    </td>
                                    <td>
                                        <div id="dltp_<?= $i ?>">
                                            <input type='checkbox' name='deletepermanent[]' value="<?= $row ?>" onclick="javascript: if(addn_<?php echo $i ?>.style.display=='none' ){addn_<?php echo $i ?>.style.display='block' }else{addn_<?php echo $i ?>.style.display='none' }\" />
                                        </div>
                                    </td>
                                </tr>
                                <?php $i++; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG["CLOSE"] ?></button>
                    <button type="submit" class="btn btn-sm ttbtn"><?= LANG["SUBMIT"] ?></button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php block_end() ?>