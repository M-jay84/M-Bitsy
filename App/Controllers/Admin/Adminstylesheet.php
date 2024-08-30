<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\StylesheetsModel;

/*
* Style Controller (Home Page)
*/
class Adminstylesheet extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = (new Auth)->user(2);
        if ($this->current_user->class < CONFIG['_MODERATOR']) {
            showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
        }
    }

    // Themes Default Page
    public function index()
    {
    
        // Get Themes Data
        $styles = (new StylesheetsModel)->fetchAll('*', '');

        // Data For View
        $data = [
            'page_title' => LANG["THEMES"],
            'V_styles' => $styles
        ];

        // Load View
        $this->view('stylesheet', $data, 'admin');
    }

    // Add Theme Default Page
    public function submit()
    {
    
        (new StylesheetsModel)->insert();
    }

    // Delete Theme Form Submit
    public function delete()
    {
    
        (new StylesheetsModel)->delete();
    }
}