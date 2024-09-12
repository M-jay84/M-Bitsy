<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;

/*
* Logout Controller
*/
class Logout extends Controller
{
    // Destroy $_COOKIE/$_SESSION
    public function index()
    {
        Auth::destroy_cookies();
        $this->redirect('login');
    }

}