<?php

namespace App\Libs;

/*
* Cache
*/
class Controller
{
    public $currentuser = null;
    public $view = null;
    public $model = null;
    public $data = null;

    // Display View Template
    public function view($file, $data = [], $page = false)
    {
        $this->view = View::template($file, $data, $page);

    }
    
    public function showMessage($url, $message1)
    {
        showMessage(Url($url), $message1);
        return;
    }

        
    public function redirect($url)
    {
        $url = CONFIG['URLROOT'] . $url;
        if (!headers_sent()) {
            header("Location: " . $url, true, 302);
            exit();
        } else {
            echo '<script type="text/javascript">';
            echo 'window.location.href="' . $url . '";';
            echo '</script>';
            echo '<noscript>';
            echo '<meta http-equiv="refresh" content="3; url=' . $url . '" />';
            echo '</noscript>';
            exit();
        }
    }
}