<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\UsersModel;

/*
* Stylesheet/Language Controller
*/
class Stylesheet 
{
    // Style/Language Block Form Submit
    public function index()
    {
        $USER = new User(0, 2);
        // Check Input Else Return To Form
        if (!empty($_POST) && csrf_check()) {
            $stylesheet = Input('stylesheet');
            $language = Input('language');
            UsersModel::update(['stylesheet','language'], ['id='], [$stylesheet, $language, $USER->has('id')]);
            Redirect(Url());
        }
        Redirect(Url(), Txt('Error'));
    }
}