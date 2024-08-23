<?php

namespace App\Controllers\User;

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
        $this->current_user = $this->currentuser(1);
    }

    // Download Attachment Page
    public function index($id, $hash)
    {
        $file = CONFIG['UPLOADDIR'] . "/attachment/$hash.data";
        $attachment = (new AttachmentsModel)->fetchRow('*', ['id ='], [$id]);
        $extension = substr($attachment['filename'], -3);
        
        // Download
        if (!file_exists($file)) {
            showMessage(Url($_SERVER['HTTP_REFERER']), "The file $hash does not exists");
        } else {
            header('Content-Disposition: attachment; filename="' . $attachment['filename'] . '"');
            header('Content-Length: ' . filesize($file));
            header("Content-Type: application/$extension");
            readfile($file);
        }
    }

}