<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\StylesheetsModel;

/*
* Style Controller (Home Page)
*/
class Adminstylesheet 
{
    // Themes Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        // Get Themes Data
        $styles = StylesheetsModel::fetchAll('*', '');

        // Init Data
        $data = [
            'V_title' => Txt("THEMES"),
            'V_styles' => $styles,
            'USER' => $USER
        ];

        // Load View
        template('stylesheet', $data, 'admin');
    }

    // Add Theme Default Page
    public function submit()
    {
        $USER = new User(_MODERATOR, 2);
        StylesheetsModel::insert();
    }

    // Delete Theme Form Submit
    public function delete()
    {
        $USER = new User(_MODERATOR, 2);
        StylesheetsModel::delete();
    }
}