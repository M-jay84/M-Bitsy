<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\UsersModel;

/*
* Cheats Controller
*/
class Admincheat 
{
    // Cheats Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        $megabts = Input('megabts') ?? 1073741824;
        $daysago = Input('daysago') ?? 30;

        $timeago = 84600 * $daysago; // last 7 days
        $bytesover = 1048576 * $megabts; // over 500MB Upped
        $users = UsersModel::fetchAll(
            '*', ["UNIX_TIMESTAMP(?) - UNIX_TIMESTAMP(last_access) < ", 'status=', 'uploaded > '],
                 [get_date_time(), $timeago, 'confirmed', $bytesover], 
                 'ORDER BY uploaded DESC');
        
        $message = "<p>" . $users->rowCount() . " Users with found over last " . $daysago . " days with more than " . $megabts . " MB (" . mksize($bytesover) . ") Bytes Uploaded.</p>";

        // Init Data
        $data = [
            'V_title' => Txt("CHEAT"),
            'V_users' => $users,
            'V_message' => $message,
            'USER' => $USER
        ];

        // Load View
        template('cheats', $data, 'admin');
    }

}