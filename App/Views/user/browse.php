<?php

use App\Models\CatagoriesModel;
use App\Models\UsersModel;

 block_begin($V_title) ?>
<div class="tt_blockcenter rounded">
<form method="get" action="<?= Url('browse') ?>">
<p class="text-center">
<?php
$i = 0;
foreach ($V_cats as $cat) {
    $catsperrow = 5;
    print(($i && $i % $catsperrow == 0) ? "<br><br>" : "");
    $get_cat = isset($_GET["cat"]) ? $_GET["cat"] : '';
    print("<a href=" . Url("browse?cat=$cat[id]") . ">".htmlspecialchars($cat["parent_cat"])." - " . htmlspecialchars($cat["name"]) . "</a> <input name='c{$cat["id"]}' type=\"checkbox\" " . (in_array($cat["id"], $V_wherecatina) || $get_cat == $cat["id"] ? "checked='checked' " : "") . "value='1' />&nbsp;\n");
    $i++;
}
echo "<br><br><a href='". Url('browse') ."' type='submit' class='ttbtn'>".Txt("RESET")."</a>&nbsp;<input type='submit' class='ttbtn' value='".Txt("GO")."' />";
echo "</p></form></div>";
//if we are browsing, display all subcats that are in same cat
if ($V_parent_cat) {
    $V_url .= "parent_cat=" . urlencode($V_parent_cat) . "&amp;";
    echo "<br /><br /><b>" . Txt("YOU_ARE_IN") . ":</b> <a href='" . Url("browse?parent_cat=" . urlencode($V_parent_cat) . "") . "'>" . htmlspecialchars($V_parent_cat) . "</a><br /><b>" . Txt("SUB_CATS") . ":</b> ";
    $subcatsquery = CatagoriesModel::fetchAll('id, name, parent_cat', ['parent_cat='], [$V_parent_cat], 'ORDER BY name');
    foreach ($subcatsquery as $subcatsrow) {
        $name = $subcatsrow['name'];
        echo " - <a href=" . Url("browse?cat=$subcatsrow[id]") . ">$name</a>";
    }
}

if (Id($V_pager)) {
    $V_url .= "page=$_GET[page]&amp;";
}

echo "</center><br /><br />"; //some spacing
// New code (TorrentialStorm)
echo "<div align='right'><form id='sort' action=''>" . Txt("SORT_BY") . ": <select name='sort' onchange='window.location=\"{$V_url}sort=\"+this.options[this.selectedIndex].value+\"&amp;order=\"+document.forms[\"sort\"].order.options[document.forms[\"sort\"].order.selectedIndex].value'>";
echo "<option value='id'" . ($_GET["sort"] == "id" ? " selected='selected'" : "") . ">" . Txt("ADDED") . "</option>";
echo "<option value='name'" . ($_GET["sort"] == "name" ? " selected='selected'" : "") . ">" . Txt("NAME") . "</option>";
echo "<option value='comments'" . ($_GET["sort"] == "comments" ? " selected='selected'" : "") . ">" . Txt("COMMENTS") . "</option>";
echo "<option value='size'" . ($_GET["sort"] == "size" ? " selected='selected'" : "") . ">" . Txt("SIZE") . "</option>";
echo "<option value='times_completed'" . ($_GET["sort"] == "times_completed" ? " selected='selected'" : "") . ">" . Txt("COMPLETED") . "</option>";
echo "<option value='seeders'" . ($_GET["sort"] == "seeders" ? " selected='selected'" : "") . ">" . Txt("SEEDERS") . "</option>";
echo "<option value='leechers'" . ($_GET["sort"] == "leechers" ? " selected='selected'" : "") . ">" . Txt("LEECHERS") . "</option>";
echo "</select>&nbsp;";
echo "<select name='order' onchange='window.location=\"{$V_url}order=\"+this.options[this.selectedIndex].value+\"&amp;sort=\"+document.forms[\"sort\"].sort.options[document.forms[\"sort\"].sort.selectedIndex].value'>";
echo "<option selected='selected' value='asc'" . ($_GET["order"] == "asc" ? " selected='selected'" : "") . ">" . Txt("ASCEND") . "</option>";
echo "<option value='desc'" . ($_GET["order"] == "desc" ? " selected='selected'" : "") . ">" . Txt("DESCEND") . "</option>";
echo "</select>";
echo "</form></div>";
// End

if ($V_count) {
    torrenttable($USER, $V_torrents);
    print($V_pager);
} else {
    print(Txt("NOTHING_FOUND") . "&nbsp;&nbsp;");
    print Txt("NO_UPLOADS");
}
if ($_SESSION) {
    UsersModel::update(['last_access'], ['id='], [get_date_time(),$USER->has('id')]);
}
block_end() ?>