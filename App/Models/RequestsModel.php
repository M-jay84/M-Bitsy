<?php

namespace App\Models;

use PDO;
use App\Libs\DB;
use App\Models\ShoutboxModel;
use App\Models\LogsModel;
use App\Models\MessagesModel;

/*
* Request Table Data
*/
class RequestsModel
{
    // Get Request
    public function pages($id = '')
    {
        if (empty($id)) {
            $count = DB::run("SELECT count(requests.id) 
                FROM requests 
                inner join categories 
                on requests.cat = categories.id 
                inner join users 
                on requests.userid = users.id"
            )->fetchColumn();;
            list($pager, $limit) = pagination(30, $count, CONFIG['URLROOT'] . "/request?" . "category=" . $_GET["category"] . "&sort=" . $_GET["sort"] . "&");
            $res = DB::run("SELECT users.downloaded, 
            users.uploaded, 
            users.username, 
            users.privacy, 
            requests.filled, 
            requests.comments,
            requests.filledby, 
            requests.id, 
            requests.descr, 
            requests.userid, 
            requests.request, 
            requests.added, 
            requests.hits, 
            categories.name as cat,
            categories.parent_cat as parent_cat
     FROM requests 
     inner join categories 
     on requests.cat = categories.id 
     inner join users 
     on requests.userid = users.id $limit")->fetchAll();
        } else {
            $count = DB::run("SELECT count(requests.id) 
                FROM requests 
                inner join categories 
                on requests.cat = categories.id 
                inner join users on requests.userid = users.id 
                where userid = $id"
            )->fetchColumn();;
            list($pager, $limit) = pagination(30, $count, CONFIG['URLROOT'] . "/request?" . "category=" . $_GET["category"] . "&sort=" . $_GET["sort"] . "&");
            $res = DB::run("SELECT users.downloaded,
                users.uploaded, 
                users.username,
                users.privacy,
                requests.filled,
                requests.comments,
            requests.filledby, 
            requests.id, 
            requests.descr, 
            requests.userid, 
            requests.request, 
            requests.added, 
            requests.hits, 
            categories.name as cat,
            categories.parent_cat as parent_cat
     FROM requests 
     inner join categories 
     on requests.cat = categories.id 
     inner join users 
     on requests.userid = users.id WHERE userid = $id"
            )->fetchAll();
        }
        $array = [
            'res' => $res,
            'pager' => $pager
        ];
        return $array;
    }

    // Add Request
    public function create($current_user)
    {
        $requesttitle = Input('requesttitle');
        $cat = Input('cat', 'int');
        $descr = Input('descr');

        if (!$requesttitle) {
            showMessage(Url('request'), LANG["MISSING_FORM_DATA"]);
        } elseif ($cat == 0) {
            showMessage(Url('request'), LANG["MISSING_ID"]);
        }

        
        DB::insert('requests', ['hits' => 1, 'userid' => $current_user->id, 'cat' => $cat, 'request' => $requesttitle, 'descr' => $descr, 'added' => get_date_time(), 'comments' => 0]);
        $id = DB::lastInsertId();

        DB::insert('addedrequests', ['requestid' => $id, 'userid' => $current_user->id]);

        $msg = "[color=".$current_user->Color."]".$current_user->username."[/color]  has made a request for [url=" . CONFIG['URLROOT'] . "/request]" . $requesttitle . "[/url]";
        (new ShoutboxModel)->insert(['user' => 'System', 'message' => $msg, 'date' => get_date_time(), 'userid' => 0]);
        (new LogsModel)->write("$requesttitle was added to the Request section");
        Url(Url('request'), true);
    }

    // Edit Request
    public function edit()
    {
        $id = Input('id', 'int');
        if (!Id($id)) {
            showMessage(Url('request'), LANG["MISSING_ID"]);
        }

        $descr = Input('desc');
        $cat = Input('cat', 'int');
        $filled = Input('filled', 'int');
        $request = Input('request', 'int');
        $filledby = Input('filledby', 'int');

        if (!empty($_POST)) {
            (new RequestsModel)->edit($cat, $request, $descr, $filledby, $filled, $id);
        }
        if (!$filled) {
            DB::update('requests', ['cat','request','descr','filledby','filled'], ['id='], [$cat, $request, $descr, $filledby, $filled, $id]);
        } else {
            DB::update('requests', ['cat','request','descr','filledby','filled'], ['id='], [$cat, $request, $descr, $filledby, $filled, $id]);
        }
        Url(Url("request"), true);
    }

    // Fill Request
    public function fill($requestid, $current_userid)
    {
        $filledurl = Input('filledurl');

        
        $res = DB::run("SELECT users.username, requests.userid, requests.request FROM requests inner join users on requests.userid = users.id where requests.id = $requestid");
        $arr = $res->fetch(PDO::FETCH_ASSOC);

        $msg = "Your request $requestid ";
        $msg2 = "Your Request Filled";
        DB::update('requests', ['filledby','filled'], ['id='], [$current_userid, $filledurl, $requestid]);
        MessagesModel::insert(['poster' => 0, 'sender' => 0, 'sender' => $arr['userid'], 'added' => get_date_time(), 'subject' => $msg2, 'msg' => $msg]);
        showMessage(Url('request'), "Request $requestid was successfully filled with <a href=$filledurl>$filledurl</a>.  User <a href=" . CONFIG['URLROOT'] . "/profile/$arr[userid]><b>$arr[username]</b></a> automatically PMd.  <br>Filled that accidently? No worries, <a href=" . CONFIG['URLROOT'] . "/request/reqreset/$requestid>CLICK HERE</a> to mark the request as unfilled.  Do <b>NOT</b> follow this link unless you are sure there is a problem.");
    }

    // Vote Request
    public static function vote($requestid, $userid)
    {
        $voted = DB::fetchRow('addedrequests', '*', ['requestid=', 'userid='], [$requestid, $userid]);
        if ($voted) {
            showMessage(Url('request'), 'Youve already voted for this request only 1 vote for each request is allowed');
        } else {
            DB::updateset('requests', ['hits' => 'hits + 1'], ['id' => $requestid]);
            DB::insert('addedrequests', ['requestid' => $requestid, 'userid' => $userid]);
            showMessage(Url('request'), "<p>Successfully voted for request $requestid</p><p>Back to <a href=" . CONFIG['URLROOT'] . "/request><b>requests</b></a></p>");
        }
    }

    // Delete Request
    public function delete($current_user)
    {
        $delreq = Input('delreq');
        
        if (($current_user->class) > CONFIG['_UPLOADER']) {
            if (!$delreq) {
                showMessage(Url('request'), LANG["NOTHING_SELECTED"]);
            }
            $ids = implode(", ", $_POST['delreq']);
            DB::deleteByIds('requests', 'id', $ids);
            DB::deleteByIds('addedrequests', 'requestid', $ids);
            showMessage(Url('request'), LANG["DEL_COMPLETE"]);
        } else {
            foreach ($_POST['delreq'] as $del_req) {
                $num = DB::fetchColumn('requests', 'count(id)', ['userid=', 'id='], [$current_user->id, $del_req]);
                if ($num > 0) {
                    DB::deleteByIds('requests', 'id', $del_req);
                    DB::deleteByIds('addedrequests', 'requestid', $del_req);
                    showMessage(Url('request'), "Request ID $del_req Deleted", CONFIG['URLROOT'] . "/request");
                } else {
                    showMessage(Url('request'), "No Permission to delete Request ID $del_req");
                }
            }
        }
    }

    // Make Request Unfilled
    public function reset($requestid, $current_user)
    {
        $arr = DB::fetchRow('requests', 'userid, filledby', ['id='], [$requestid]);

        if (($current_user->id == $arr['userid']) || ($current_user->class >= 4) || ($current_user->id == $arr['filledby'])) {
            DB::update('requests', ['filledby','filled'], ['id='], [0,'',$requestid]);
            showMessage(Url('request'), "Request $requestid successfully reset.");
        } else {
            showMessage(Url('request'), "Sorry, cannot reset a request when you are not the owner");
        }
    }

    // Make Request Unfilled
    public static function joinuser($requestid)
    {
        $arr = DB::run("select users.id as userid,users.username, users.downloaded,users.uploaded, 
		requests.id as requestid, requests.request 
		from addedrequests 
		inner join users on addedrequests.userid = users.id 
		inner join requests on addedrequests.requestid = requests.id 
		WHERE addedrequests.requestid =$requestid")->fetchAll();
		return $arr;
    }

    // Make Request Unfilled
    public static function joincat()
    {
        $arr = DB::run("SELECT requests.id, requests.request, categories.name AS cat, categories.id AS catid,
		categories.parent_cat AS parent_cat FROM requests INNER JOIN categories ON requests.cat = categories.id ORDER BY
		requests.id DESC LIMIT 5")->fetchAll();
		return $arr;
    }
	
    // Fetch Request Row
    public function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('requests', $columns, $where, $values, $other);
		return $stmt;
	}
	
    // Update Request Row
    public function update($targetid)
    {
        $stmt = DB::run("UPDATE requests SET comments = comments + 1 WHERE id = $targetid");
		return $stmt;
	}

}