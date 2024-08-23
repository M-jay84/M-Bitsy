<?php

namespace App\Controllers\Admin;

use App\Libs\Controller;

/*
* Exception Controller
*/
class Adminexceptions extends Controller
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

    // Exception Default Page
    public function index() {
    
        // Get File
        $exceptionfilelocation = CONFIG['LOGGER']."/exception_log.txt";
        $filecontents = file_get_contents($exceptionfilelocation);

        // Create File
        function make_content_file($exceptionfilelocation, $content, $opentype = "w")
        {
            $fp_file = fopen($exceptionfilelocation, $opentype);
            fputs($fp_file, $content);
            fclose($fp_file);
        }

        if (!empty($_POST)) {
            $newcontents = Input('newcontents');
            make_content_file($exceptionfilelocation, $newcontents);
        }

        // Data For View
        $data = [
            'page_title' => LANG['EXCEPTIONS'],
            'V_filecontents' => $filecontents
        ];

        // Load View
        $this->view('exception', $data, 'admin');
    }

}