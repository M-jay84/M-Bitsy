<?php

namespace App\Controllers\User;

use App\Libs\Controller;
use App\Models\UsersModel;

/*
* Stylesheet/Language Controller
*/
class Stylesheet extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
    }

    // Style/Language Block Form Submit
    public function index()
    {
    
        // Check Input Else Return To Form
        if (!empty($_POST) && csrf_check()) {
            $stylesheet = Input('stylesheet');
            $language = Input('language');
            UsersModel::update(['stylesheet','language'], ['id='], [$stylesheet, $language, $this->current_user->id]);
            Url(Url(), true);
        }
        showMessage(Url(), LANG['Error']);
    }
}