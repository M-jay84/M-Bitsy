<?php

use App\Models\StylesheetsModel;
use App\Models\TorrentLangModel;

if ($USER->has('id')) {
	block_begin(Txt("THEME") . " / " . Txt("LANGUAGE"));
    $stylesheets = '';
    $languages = '';
    $ss_r = StylesheetsModel::fetchAll('*', '');
    $ss_sa = array();
    foreach ($ss_r as $ss_a) {
        $ss_id = $ss_a["uri"];
        $ss_name = $ss_a["name"];
        $ss_sa[$ss_name] = $ss_id;
    }
    ksort($ss_sa);
    reset($ss_sa);
    //while (list($ss_name, $ss_id) = thisEach($ss_sa)) {
    foreach($ss_sa as $ss_name => $ss_id) {
        if ($ss_id == $USER->has('stylesheet')) {
            $ss = " selected='selected'";
        } else {
            $ss = "";
        }
        $stylesheets .= "<option value='$ss_id'$ss>$ss_name</option>\n";
    }

    $lang_r = TorrentLangModel::fetchAllLang('*', '');
    $lang_sa = array();
    foreach ($lang_r as $lang_a) {
        $lang_id = $lang_a["name"];
        $lang_name = $lang_a["name"];
        $lang_sa[$lang_name] = $lang_id;
    }
    ksort($lang_sa);
    reset($lang_sa);
    //while (list($lang_name, $lang_id) = thisEach($lang_sa)) {
    foreach($lang_sa as $lang_name => $lang_id) {
        if ($lang_id == $USER->has('language')) {
            $lang = " selected='selected'";
        } else {
            $lang = "";
        }
        $languages .= "<option value='$lang_id'$lang>$lang_name</option>\n";
    }
    ?>

    <form method="post" action="<?= Url('stylesheet') ?>" class="form-horizontal">
 	    <div class="form-group">
		    <center><label><?= Txt("THEME") ?></label></center>
		    <select name="stylesheet" style="width: 95%"  class='form-select'><?= $stylesheets; ?></select>
  	    </div>
	    <div class="form-group">
            <center><label><?= Txt("LANGUAGE") ?></label></center>
	    	<select name="language" style="width: 95%"  class='form-select'><?= $languages; ?></select>
  	    </div>
        <center><button type="submit" class="btn ttbtn center-block" value="" /><?= Txt("APPLY") ?></button></center>
    </form>
    <?php
    block_end();
}