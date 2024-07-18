<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\BlocksModel;

/*
* Portal Controller
*/
class Adminblock 
{
    // Blocks Default Page
    public function index($edit = false)
    {
        $USER = new User(_MODERATOR, 2);
        // Check Input
        if ($edit) {
            BlocksModel::editable();
        }

        // Get Block Data
        $blocks = BlocksModel::getAll();

        // Check Dir
        $in_folder = array();
        if ($folder = opendir(APPROOT . '/views/block')) {
            while (false !== ($file = readdir($folder))) {
                if ($file != "." && $file != ".." && !in_array($file, $blocks['files'])) {
                    if (preg_match("/_block.php/i", $file)) {
                        $files[] = $file;
                    }

                }
            }
            closedir($folder);
        }

        // Init Data
        $data = [
            'V_title' => Txt("PORTAL"),
            'V_blocks' => $blocks['res'],
            'V_files' => $files,
            'USER' => $USER
        ];
        
        // Load View
        template('blocks', $data, 'admin');
    }

    // Upload Block FormSubmit
    public function submit()
    {
        $USER = new User(_MODERATOR, 2);
        // Delete Block
        if ($_POST["deletepermanent"]) {
            foreach ($_POST["deletepermanent"] as $delpthis) {
                unlink(APPROOT."/views/block/" . $delpthis);
                if (file_exists(APPROOT . "/views/block/" . $delpthis)) {
                    Redirect(Url('adminportal'), Txt("ACTION_FAILED"));
                } else {
                    Redirect(Url('adminportal'), Txt("ACTION_COMPLETE"));
                }
            }
        }

        // Add Block
        if ($_POST["addnew"]) {
            foreach ($_POST["addnew"] as $addthis) {
                $i = $addthis;
                $addblock = $_POST["addblock_$i"];
                $wantedname = $_POST["wantedname_$i"];
                $name = str_replace("_block.php", "", $addblock);
                $description = $_POST["wanteddescription_" . $i];
                BlocksModel::insert(['named'=>$wantedname,'name'=>$name,'description'=>$description,'position'=>'left','enabled'=>0,'sort'=>0]);
            }
            Redirect(Url('adminportal'), Txt("SUCCESS"));
        }
    }

}