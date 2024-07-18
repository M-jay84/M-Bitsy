<?php

namespace App\Models;

use App\Libs\DB;
use App\Models\ReadModel;
use App\Models\ReportsModel;
use App\Models\GroupsModel;
use App\Models\WarningsModel;
use App\Models\LogsModel;

/*
* Users Table Data
*/
class UsersModel
{
    // Fetch Torrent Details
    public static function updateset($updateset, $id)
    {
		$stmt = DB::updateset("users", $updateset, ['id' => $id]);
        return $stmt;
	}

    // Fetch Torrent Details
    public static function insert($updateset)
    {
        $stmt = DB::insert('users', $updateset);
        return $stmt;
	}
	
    // Fetch Torrent Details
    public static function update($cols, $where, $vals, $other = '')
    {
        $stmt = DB::update('users', $cols, $where, $vals, $other);
        return $stmt;
	}

    // Fetch Torrent Details
    public static function fetchColumn($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('users', $columns, $where, $values, $other)->fetchColumn();
		return $stmt;
	}
	
    // Fetch Torrent Details
    public static function fetchAll($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('users', $columns, $where, $values, $other);
		return $stmt;
	}
		
    // Fetch Torrent Details
    public static function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('users', $columns, $where, $values, $other);
		return $stmt;
	}
		
    // Fetch Torrent Details
    public static function quickuser($id)
    {
        $stmt = DB::run(
            "SELECT u.class, u.id, u.username, u.last_access, u.country, u.age, u.gender, 
            u.avatar,  u.donated, u.warned, u.enabled, 
            g.Color, g.level, u.uploaded, u.downloaded 
		    FROM `users` `u` 
            INNER JOIN `groups` `g` 
            ON g.group_id=u.class 
            WHERE id ='" . $id . "'"
        )->fetch();
		return $stmt;
	}

    // Admin Confirm User Reg
    public static function confirmReg()
    {
        $ids = multiIds('ids');
        DB::run("UPDATE `users` SET `status` = ? WHERE `status` = ? AND `invited_by` = ? AND `id` IN (?)", ['confirmed', 'pending', 0, $ids])->fetchAll();
        Redirect(Url('adminconfirmreg'), Txt("ACTION_COMPLETE"));
    }
		
    // Confirm User Reg
    public static function updateWhereIn($array, $ids, $col = 'id')
    {
        $res = DB::updateWhereIn('users', $array, $ids, $col);
        return $res;
    }
	
    // Search Users
    public static function search($search, $class, $letter)
    {
        $q = $query = null;
        if ($search) {
            $query = "username LIKE '$search%' AND status='confirmed'";
            if ($search) {
                $q = "search=" . htmlspecialchars($search);
            }
        } elseif ($letter) {
            if (strlen($letter) > 1) {
                unset($letter);
            }
            if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz", $letter) === false) {
                unset($letter);
            } else {
                $query = "username LIKE '$letter%' AND status='confirmed'"; // todo esc
            }
            $q = "letter=$letter";
        }
        if (!$query) {
            $query = "status='confirmed'";
        }
        if (!$class) {
            unset($class);
        } else {
            $query .= " AND class=$class";
            $q .= ($q ? "&amp;" : "") . "class=$class";
        }

        $var = ['query' => $query, 'q' => $q];

        return $var;
    }

    // Get Members
    public static function getMembers()
    {
        // Check User Input
        $search = (isset($_GET['search'])) ? trim(filter_input(INPUT_GET, 'search')) : '';
        $class = (int) (isset($_GET['class'])) ? $_GET['class'] : 0;
        $letter = (isset($_GET['letter'])) ? trim(filter_input(INPUT_GET, 'letter')) : '';

        // Search Users
        $data = UsersModel::search($search, $class, $letter);

        // Pagination
        $count = DB::run("SELECT COUNT(*) FROM users WHERE $data[query]")->fetchcolumn();
        list($pager, $limit) = pagination(25, $count, URLROOT . "/members?$data[q]&");
        $results = DB::run("SELECT users.*, groups.level FROM users INNER JOIN `groups` ON groups.group_id=users.class WHERE $data[query] ORDER BY username $limit")->fetchAll();

        // Get Group Data
        $res = GroupsModel::fetchAll('group_id, level', '', 'ORDER BY group_id ASC');

        $array = [
            'groups' => $res,
            'array' => $results,
            'pager' => $pager
        ];

        return $array;
    }

    // Function That Removes All From An Account
    public static function deleteUser($userid)
    {
        DB::delete('users', ['id' => $userid]);
        WarningsModel::delete(['userid' => $userid]);
        DB::delete('ratings', ['user' => $userid]);
        DB::delete('peers', ['userid' => $userid]);
        DB::delete('completed', ['userid' => $userid]);
        ReportsModel::delete(['addedby' => $userid]);
        ReportsModel::delete(['votedfor' => $userid, 'type' => 'user']);
        ReadModel::delete($userid);
        DB::delete('pollanswers', ['userid' => $userid]);
        DB::delete('snatched', ['uid' => $userid]);
    }

    // Users Select
    public static function select($col, $where, $values, $orderby)
    {
        $stmt = DB::fetchAll('users', $col, $where, $values, $orderby);
        return $stmt;
    }

    // Users Avatars
    public static function avatarPagination()
    {
        $count = DB::fetchColumn('users','count(id)',['enabled=','avatar !='], ['yes', '']);
        list($pager, $limit) = pagination(50, $count, URLROOT . '/adminavatar?');
        $stmt = DB::fetchAll('users','username,id,avatar',['enabled=','avatar !='],['yes',''], $limit);
        $array = ['stmt' => $stmt, 'pager' => $pager];
        return $array;
    }

    // Privacy
    public static function privacyPagination($type)
    {
        $where = array();
        switch ($type) {
            case 'normal':
                $where[] = "privacy = 'normal'";
                break;
            case 'strong':
                $where[] = "privacy = 'strong'";
                break;
            default:
                break;
        }
        $where[] = "enabled = 'yes'";
        $where[] = "status = 'confirmed'";
        $where = implode(' AND ', $where);

        // Pagination
        $count = DB::run("SELECT count(id) FROM users WHERE $where")->fetchColumn();
        list($pager, $limit) = pagination(25, $count, htmlspecialchars($_SERVER['REQUEST_URI'] . '&'));
        $stmt = DB::run("SELECT id, username, class, privacy, email, ip, added, last_access FROM users WHERE $where ORDER BY username DESC $limit")->fetchAll();

        $array = ['stmt' => $stmt, 'pager' => $pager];
        return $array;
    }

    // Find Duplicaye IPs
    public static function duplicateIps()
    {
        $count = DB::run("SELECT ip FROM users GROUP BY ip HAVING count(*) > 1")->rowCount();
        if ($count == 0) {
            Redirect(Url('admincp'), Txt("NOTHING_FOUND"));
        }
        list($pager, $limit) = pagination(25, $count, 'Adminduplicateip?');
        $stmt = DB::run("SELECT id, username, class, email, ip, added, last_access, COUNT(*) as count FROM users GROUP BY ip HAVING count(*) > 1 ORDER BY id ASC $limit")->fetchAll();

        $array = ['stmt' => $stmt, 'pager' => $pager];
        return $array;
    }

    // Delete Messages
    public static function deleteByIds($ids)
    {
        DB::deleteByIds('users', 'id', $ids);
    }

    // Admin User Search
    public static function adminUserSearch($data = [])
    {
        $url = "?"; // assign url
        $wherea = []; // assign conditions
        $params = []; // assign vars

        if (!empty($data['name'])) {
            $wherea[] = "users.username LIKE '%$data[name]%'";
        }

        if (!empty($data['ratio'])) {
            $ratio = $data['ratio'];
            $wherea[] = "users.uploaded/users.downloaded > $ratio";
        }

        if (!empty($data['member'])) {
            $wherea[] = "users.status = '$data[member]'";
        }

        if (!empty($data['email'])) {
            $wherea[] = "users.email LIKE '%$data[email]%'";
        }

        if (!empty($data['ip'])) {
            $wherea[] = "users.ip LIKE '%$data[ip]%'";
        }

        if (!empty($data['account'])) {
            $wherea[] = "users.enabled = '$data[account]'";
        }

        if (!empty($data['uploaded'])) {
            $uploaded = strtobytes($data['uploaded']);
            $wherea[] = "users.uploaded > $uploaded";
        }

        if (!empty($data['class'])) {
            $wherea[] = "users.class = '$data[class]'";
        }

        if (!empty($data['downloaded'])) {
            $downloaded = strtobytes($data['downloaded']);
            $wherea[] = "users.downloaded > $downloaded";
        }

        if (!empty($data['warned'])) {
            $wherea[] = "users.warned = '$data[warned]'";
        }

        $where = implode(' AND ', $wherea);
        if ($where != '') {
            $where = 'WHERE ' . $where;
        }

        // Pagination
        $count = DB::run("SELECT COUNT(`id`) FROM `users` $where")->fetchcolumn();
        list($pager, $limit) = pagination(25, $count, URLROOT . "/adminusersearch&");
        $results = DB::run("SELECT * FROM `users` $where $limit")->fetchAll();
        $array = [
            'results' => $results,
            'pager' => $pager
        ];
        return $array;
    }

    // Trade Snatch
    public static function updatebonus($torsize, $modcomment, $uid)
    {
        $stmt = DB::run("UPDATE users SET seedbonus = seedbonus - ?, modcomment = ? WHERE id = ?", [$torsize, $modcomment, $uid]);
	}
		
    // Trade Snatch
    public static function updateupload($torsize, $modcomment, $uid)
    {
        $stmt = DB::run("UPDATE users SET uploaded = uploaded - '$torsize', modcomment = ? WHERE id = ?", [$modcomment, $uid]);
		return $stmt;
	}
		
    // Message ajax member search
    public static function getUserName()
    {
        $stmt = DB::run("SELECT id,username FROM users WHERE username like '" . $_POST["keyword"] . "%' ORDER BY username LIMIT 0,6");
		return $stmt;
	}
		
    // Downloading User
    public static function downloadUser($passkey)
    {
        $stmt = DB::run("SELECT users.id, users.class, users.downloadbanned, users.role_id FROM `users` 
                LEFT OUTER JOIN `groups` 
                ON users.class=groups.group_id 
                WHERE passkey = ?",
                [$passkey]
            )->fetch(\PDO::FETCH_ASSOC);
		return $stmt;
	}
				
    // User whew inactive?
    public static function selectWhereIn($option = false)
    {
        if ($option) {
			$stmt = DB::run("SELECT id, email , username, added, last_access FROM users WHERE id IN (" . implode(", ", $_POST['userid']) . ") ORDER BY last_access DESC ");
		} else {
			$stmt = DB::run("SELECT id, modcomment FROM users WHERE id IN (" . implode(", ", $_POST['userid']) . ") ORDER BY id DESC ");
		}
		return $stmt;
	}
				
    // Admin Get Emails
    public static function getEmail($ids)
    {
        $stmt = DB::run("SELECT u.email FROM users u LEFT JOIN `groups` g ON u.class = g.group_id WHERE u.enabled = 'yes' AND u.status = 'confirmed' AND u.class IN ($ids)");
		return $stmt;
	}

    // Admin Add User
    public static function adduser() {
        // Check User Input
        $username= Input('username');
        $password= Input('password');
        $email= Input('email');
        $password2= Input('password2');
        $secret = mksecret();
        $passhash = password_hash($password, PASSWORD_BCRYPT);
            
        // Check Correct Input
        if ($username == "" || $password == "" || $email == "") {
            Redirect(Url('adminuseradd'), Txt("MISSING_FORM_DATA"));
        }

        if ($password != $password2) {
            Redirect(Url('adminuseradd'), Txt("PASSWORD_NOT_MATCH"));
        }
            
        // Insert
        $count = UsersModel::fetchColumn('count(id)', ['username ='], [$username]);
        if ($count !=0) {
            Redirect(Url('adminuseradd'), Txt("NAME_IN_USE"));
        }
        UsersModel::insert(['added'=>get_date_time(), 'last_access'=>get_date_time(), 'secret'=>$secret, 'username'=>$username, 'password'=>$passhash, 'status'=>'confirmed', 'email'=>$email]);
        Redirect(Url('adminuseradd'), Txt("SUCCESS"));
    }

}