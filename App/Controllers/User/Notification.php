<?php

namespace App\Controllers\User;

use App\Libs\Controller;
use App\Models\NotificationsModel;

/*
* Notifications Controller
*/
class Notification extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
    }

    // Notification Page
    public function index()
    {
    
        $notifs = (new NotificationsModel)->getAll($this->current_user->id);
        if ($notifs['count']) {
            // Data For View
            $data = [
                'page_title' => LANG['Notifications'],
                'V_notifications' => $notifs['result']
            ];
            // Load View
            $this->view('notification', $data, 'user');
        } else {
            showMessage(Url(), LANG['NOTHING_FOUND']);
        }
    }

    // Notification Read // TODO
    public function read($id)
    {
    
        (new NotificationsModel)->updateRead($id, $this->current_user->id);
        $notif = (new NotificationsModel)->getById($id, $this->current_user->id);
        
        // Data For View
        $data = [
            'page_title' => LANG['Notifications'],
            'V_notifications' => $notif
        ];

        // Load View
        $this->view('notification_read', $data, 'user');
    }

    // Submit Read
    public function submit($id)
    {
    
        if (!empty($_GET)) {
            $read = (new NotificationsModel)->updateRead($id, $this->current_user->id);
            if ($read) {
                showMessage(Url('notification'), 'Marked Read');
            } else {
                showMessage(Url('notification'), 'Error Marking Read');
            }
        }
    }

    // Notification Delete
    public function delete($id)
    {
        (new NotificationsModel)->deleteById($id, $this->current_user->id);
    }

}