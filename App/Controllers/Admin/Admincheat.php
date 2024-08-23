<?php

namespace App\Controllers\Admin;

use App\Libs\Controller;
use App\Models\UsersModel;

/*
* Cheats Controller
*/
class Admincheat extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
        if ($this->current_user->class < CONFIG['_MODERATOR']) {
            showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
        }
    }

    // Cheats Default Page
    public function index()
    {
    
        $megabts = Input('megabts') ?? 1073741824;
        $daysago = Input('daysago') ?? 30;

        $timeago = 84600 * $daysago; // last 7 days
        $bytesover = 1048576 * $megabts; // over 500MB Upped
        $users = UsersModel::fetchAll(
            '*', ["UNIX_TIMESTAMP(?) - UNIX_TIMESTAMP(last_access) < ", 'status=', 'uploaded > '],
                 [get_date_time(), $timeago, 'confirmed', $bytesover], 
                 'ORDER BY uploaded DESC');
        
        $message = "<p>" . $users->rowCount() . " Users with found over last " . $daysago . " days with more than " . $megabts . " MB (" . mksize($bytesover) . ") Bytes Uploaded.</p>";

        // Data For View
        $data = [
            'page_title' => LANG["CHEAT"],
            'V_users' => $users,
            'V_message' => $message
        ];

        // Load View
        $this->view('cheats', $data, 'admin');
    }

}