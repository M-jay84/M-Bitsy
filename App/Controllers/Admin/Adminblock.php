<?php

namespace App\Controllers\Admin;

use App\Libs\Controller;
use App\Models\BlocksModel;

/*
* Portal Controller
*/
class Adminblock extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
        if ($this->current_user->class < CONFIG['_MODERATOR']) {
            showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
        }
    }

    // Blocks Default Page
    public function index($edit = false)
    {
    
        // Check Input
        if ($edit) {
            (new BlocksModel)->editable();
        }

        // Get Block Data
        $blocks = (new BlocksModel)->getAll();

        // Check Dir
        $in_folder = array();
        if ($folder = opendir(CONFIG['APPROOT'] . '/views/block')) {
            while (false !== ($file = readdir($folder))) {
                if ($file != "." && $file != ".." && !in_array($file, $blocks['files'])) {
                    if (preg_match("/_block.php/i", $file)) {
                        $files[] = $file;
                    }

                }
            }
            closedir($folder);
        }

        // Data For View
        $data = [
            'page_title' => LANG["PORTAL"],
            'V_blocks' => $blocks['res'],
            'V_files' => $files
        ];
        
        // Load View
        $this->view('blocks', $data, 'admin');
    }

    // Upload Block FormSubmit
    public function submit()
    {
    
        // Delete Block
        if ($_POST["deletepermanent"]) {
            foreach ($_POST["deletepermanent"] as $delpthis) {
                unlink(CONFIG['APPROOT']."/views/block/" . $delpthis);
                if (file_exists(CONFIG['APPROOT'] . "/views/block/" . $delpthis)) {
                    showMessage(Url('adminportal'), LANG["ACTION_FAILED"]);
                } else {
                    showMessage(Url('adminportal'), LANG["ACTION_COMPLETE"]);
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
                (new BlocksModel)->insert(['named'=>$wantedname,'name'=>$name,'description'=>$description,'position'=>'left','enabled'=>0,'sort'=>0]);
            }
            showMessage(Url('adminportal'), LANG["SUCCESS"]);
        }
    }

}