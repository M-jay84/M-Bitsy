<?php

namespace App\Controllers\User;

/*
* Logout Controller
*/
class Logout 
{
    // Destroy $_COOKIE/$_SESSION
    public function index()
    {
        destroy_cookies();
        Redirect(Url('login'));
    }

}