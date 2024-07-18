<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\NotificationsModel;

/*
* Notifications Controller
*/
class Notification 
{
    // Notification Page
    public function index()
    {
        $USER = new User(0, 2);
        $notifs = NotificationsModel::getAll();
        if ($notifs['count']) {
            // Init Data
            $data = [
                'V_title' => Txt('Notifications'),
                'V_notifications' => $notifs['result'],
                'USER' => $USER
            ];
            // Load View
            template('notification', $data, 'user');
        } else {
            Redirect(Url(), Txt('NOTHING_FOUND'));
        }
    }

    // Notification Read // TODO
    public function read($id)
    {
        $USER = new User(0, 2);
        NotificationsModel::updateRead($id);
        $notif = NotificationsModel::getById($id);
        
        // Init Data
        $data = [
            'V_title' => Txt('Notifications'),
            'V_notifications' => $notif,
            'USER' => $USER
        ];

        // Load View
        template('notification_read', $data, 'user');
    }

    // Submit Read
    public function submit($id)
    {
        $USER = new User(0, 2);
        if (!empty($_GET)) {
            $read = NotificationsModel::updateRead($id);
            if ($read) {
                Redirect(Url('notification'), 'Marked Read');
            } else {
                Redirect(Url('notification'), 'Error Marking Read');
            }
        }
    }

    // Notification Delete
    public function delete($id)
    {
        $USER = new User(0, 2);
        NotificationsModel::deleteById($id);
    }

}