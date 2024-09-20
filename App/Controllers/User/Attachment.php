<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\AttachmentsModel;

/*
* Attachment Controller
*/
class Attachment extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!$this->current_user->loggedin && CONFIG['MEMBERSONLY']) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Download Attachment
    public function index($id, $hash)
    {
        $file = UPLOAD_PATH . "/attachment/$hash.data";
        $attachment = AttachmentsModel::fetchRow('*', ['id ='], [$id]);
        $extension = substr($attachment['filename'], -3);
        
        // Download
        if (!file_exists($file)) {
            showMessage(URLROOT, "The file $hash does not exists");
        } else {
            header('Content-Disposition: attachment; filename="' . $attachment['filename'] . '"');
            header('Content-Length: ' . filesize($file));
            header("Content-Type: application/$extension");
            readfile($file);
        }
    }

}