<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\BansModel;

/*
* Ban Controller
*/
class Adminban
{
    // Email & IP Bans
    public function index($type = 'email')
    {
        $USER = new User(_MODERATOR, 2);
        if (!empty($_POST)) {
            BansModel::insert();
        } else {
            // Get Ban Data
            $bans = BansModel::getAll($type);

            // Init Data
            $data = [
                'V_title' => Txt("BANNED"),
                'V_type' => $type,
                'V_bans' => $bans['bans'],
                'V_pager' => $bans['pager'],
                'USER' => $USER
            ];

            // Load View
            template('ban', $data, 'admin');
        }
    }

    // Delete Bans
    public function delete()
    {
        $USER = new User(_MODERATOR, 2);
        BansModel::delete();
    }
}