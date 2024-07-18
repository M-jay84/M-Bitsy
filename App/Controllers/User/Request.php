<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\CatagoriesModel;
use App\Models\RequestsModel;

/*
* Request Controller
*/
class Request 
{
    // Request Page
    public function index($id = 0)
    {
        $USER = new User(0, 2);
        if (!$USER->permission('view_torrents')) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        }
        if (!CONFIG['REQUESTSON']) {
            Redirect(Url(), Txt("REQ_OFF"));
        }
        $requests = RequestsModel::pages($id);
   
        $data = [
            'V_title' => Txt('REQUEST'),
            'V_pager' => $requests['pager'],
            'V_cats' => CatagoriesModel::genreList(),
            'V_requests' => $requests['res'],
            'USER' => $USER
        ];
        template('request', $data, 'user');
    }

    // Confirm Request
    public function confirmreq()
    {
        $USER = new User(0, 2);
        if (!$USER->permission('view_torrents')) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        }
        if (!CONFIG['REQUESTSON']) {
            Redirect(Url(), Txt("REQ_OFF"));
        }
        RequestsModel::create($USER);
    }

    // Edit Request
    public function edit()
    {
        $USER = new User(0, 2);
        if (!$USER->permission('view_torrents')) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        }
        if (!CONFIG['REQUESTSON']) {
            Redirect(Url(), Txt("REQ_OFF"));
        }
        RequestsModel::edit();
    }

    // Delete Request
    public function delete()
    {
        $USER = new User(0, 2);
        if (!$USER->permission('view_torrents')) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        }
        if (!CONFIG['REQUESTSON']) {
            Redirect(Url(), Txt("REQ_OFF"));
        }
        RequestsModel::delete();
    }

    // Request Filled
    public function reqfilled($requestid)
    {
        $USER = new User(0, 2);
        if (!$USER->permission('view_torrents')) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        }
        if (!CONFIG['REQUESTSON']) {
            Redirect(Url(), Txt("REQ_OFF"));
        }
        RequestsModel::fill($requestid);
    }

    // Request Votes
    public function addvote($requestid)
    {
        $USER = new User(0, 2);
        if (!$USER->permission('view_torrents')) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        }
        if (!CONFIG['REQUESTSON']) {
            Redirect(Url(), Txt("REQ_OFF"));
        }
        RequestsModel::vote($requestid, $USER->has('id'));
    }

    // Reset Request
    public function reqreset($requestid)
    {
        $USER = new User(0, 2);
        if (!$USER->permission('view_torrents')) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        }
        if (!CONFIG['REQUESTSON']) {
            Redirect(Url(), Txt("REQ_OFF"));
        }
        RequestsModel::reset($requestid);
    }

}