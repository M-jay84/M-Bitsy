<?php

namespace App\Controllers\Admin;

use App\Libs\User;

/*
* Exception Controller
*/
class Adminexceptions 
{
    // Exception Default Page
    public function index() {
        $USER = new User(_MODERATOR, 2);
        // Get File
        $exceptionfilelocation = LOGGER."/exception_log.txt";
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

        // Init Data
        $data = [
            'V_title' => Txt('EXCEPTIONS'),
            'V_filecontents' => $filecontents,
            'USER' => $USER
        ];

        // Load View
        template('exception', $data, 'admin');
    }

}