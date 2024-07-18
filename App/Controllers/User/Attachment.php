<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\AttachmentsModel;

/*
* Attachment Controller
*/
class Attachment 
{
    // Download Attachment Page
    public function index($id, $hash)
    {
        $USER = new User(0, 1);
        $file = UPLOADDIR . "/attachment/$hash.data";
        $attachment = AttachmentsModel::fetchRow('*', ['id ='], [$id]);
        $extension = substr($attachment['filename'], -3);
        
        // Download
        if (!file_exists($file)) {
            Redirect($_SERVER['HTTP_REFERER'], "The file $hash does not exists");
        } else {
            header('Content-Disposition: attachment; filename="' . $attachment['filename'] . '"');
            header('Content-Length: ' . filesize($file));
            header("Content-Type: application/$extension");
            readfile($file);
        }
    }

}