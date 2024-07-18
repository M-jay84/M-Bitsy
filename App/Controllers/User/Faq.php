<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\FaqModel;

/*
* Faq Controller
*/
class Faq 
{
    // F.&.Q Page
    public function index()
    {
        $USER = new User(0, 1);
        // Get Data
        $faqs = FaqModel::select();
        
        // Init Data
        $data = [
            'V_title' => Txt("FAQ"),
            'V_faqs' => $faqs,
            'USER' => $USER
        ];
        
        // Load View
        template('faq', $data, 'user');
    }

    // Delete FAQ
    public function delete($id, $sure = 0)
    {
        $USER = new User(0, 1);
        if ($USER->isStaff()) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        } else {
            FaqModel::delete($id, $sure);
        }
    }

    // Add/Edit Section
    public function section()
    {
        $USER = new User(0, 1);
        if ($USER->isStaff()) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        } else {
            FaqModel::insertOrUpdateSection();
        }
    }

    public function item()
    {
        $USER = new User(0, 1);
        if ($USER->isStaff()) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        } else {
            FaqModel::insertOrUpdateItem();
        }
    }

}