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

    // Set Current User
    public function currentuser($force = 0, $autoclean = false)
    {
        $this->data = (new Auth)->user($force, $autoclean);
        return $this->data;
    }

    // Get User Value
    public function get($var){
        if ($this->currentuser[$var]) {
            return $this->currentuser[$var];
        } else {
            return 0;
        }
    }

    // Display View Template
    public function view($file, $data = [], $page = false)
    {
        $this->view = View::template($file, $data, $page);

    }
    
    public function showMessage($url, $message1)
    {
        showMessage($url, $message1);
        return;
    }
}