<?php

namespace App\Controllers\User;

use App\Libs\Controller;
use App\Models\CatagoriesModel;
use App\Models\RequestsModel;

/*
* Request Controller
*/
class Request extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
    }

    // Request Page
    public function index($id = 0)
    {
    
        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
            showMessage(Url(), LANG["NO_PERMISSION"]);
        }
        if (!CONFIG['REQUESTSON']) {
            showMessage(Url(), LANG["REQ_OFF"]);
        }
        $requests = (new RequestsModel)->pages($id);
   
        $data = [
            'page_title' => LANG['REQUEST'],
            'V_pager' => $requests['pager'],
            'V_cats' => CatagoriesModel::genreList(),
            'V_requests' => $requests['res'],
            'current_user' => $this->current_user
        ];
        $this->view('request', $data, 'user');
    }

    // Confirm Request
    public function confirmreq()
    {
    
        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
            showMessage(Url(), LANG["NO_PERMISSION"]);
        }
        if (!CONFIG['REQUESTSON']) {
            showMessage(Url(), LANG["REQ_OFF"]);
        }
        (new RequestsModel)->create($this->current_user);
    }

    // Edit Request
    public function edit()
    {
    
        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
            showMessage(Url(), LANG["NO_PERMISSION"]);
        }
        if (!CONFIG['REQUESTSON']) {
            showMessage(Url(), LANG["REQ_OFF"]);
        }
        (new RequestsModel)->edit();
    }

    // Delete Request
    public function delete()
    {
    
        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
            showMessage(Url(), LANG["NO_PERMISSION"]);
        }
        if (!CONFIG['REQUESTSON']) {
            showMessage(Url(), LANG["REQ_OFF"]);
        }
        (new RequestsModel)->delete($this->current_user);
    }

    // Request Filled
    public function reqfilled($requestid)
    {
    
        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
            showMessage(Url(), LANG["NO_PERMISSION"]);
        }
        if (!CONFIG['REQUESTSON']) {
            showMessage(Url(), LANG["REQ_OFF"]);
        }
        (new RequestsModel)->fill($requestid, $this->current_user);
    }

    // Request Votes
    public function addvote($requestid)
    {
    
        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
            showMessage(Url(), LANG["NO_PERMISSION"]);
        }
        if (!CONFIG['REQUESTSON']) {
            showMessage(Url(), LANG["REQ_OFF"]);
        }
        (new RequestsModel)->vote($requestid, $this->current_user->id);
    }

    // Reset Request
    public function reqreset($requestid)
    {
    
        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
            showMessage(Url(), LANG["NO_PERMISSION"]);
        }
        if (!CONFIG['REQUESTSON']) {
            showMessage(Url(), LANG["REQ_OFF"]);
        }
        (new RequestsModel)->reset($requestid, $this->current_user);
    }

}