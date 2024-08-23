<?php

namespace App\Controllers\User;

use App\Libs\Controller;
use App\Models\FaqModel;

/*
* Faq Controller
*/
class Faq extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(1);
    }

    // F.&.Q Page
    public function index()
    {
        // Get Data
        $faqs = (new FaqModel)->select();
        
        // Data For View
        $data = [
            'page_title' => LANG["FAQ"],
            'V_faqs' => $faqs,
            'current_user' => $this->current_user
        ];
        
        // Load View
        $this->view('faq', $data, 'user');
    }

    // Delete FAQ
    public function delete($id, $sure = 0)
    {
        if ($this->current_user->class > CONFIG['_MODERATOR']) {
            showMessage(Url(), LANG["NO_PERMISSION"]);
        } else {
            (new FaqModel)->delete($id, $sure);
        }
    }

    // Add/Edit Section
    public function section()
    {
        if ($this->current_user->class > CONFIG['_MODERATOR']) {
            showMessage(Url(), LANG["NO_PERMISSION"]);
        } else {
            (new FaqModel)->insertOrUpdateSection();
        }
    }

    public function item()
    {
        if ($this->current_user->class > CONFIG['_MODERATOR']) {
            showMessage(Url(), LANG["NO_PERMISSION"]);
        } else {
            (new FaqModel)->insertOrUpdateItem();
        }
    }

}