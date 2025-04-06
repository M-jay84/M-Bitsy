<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Libs\DB;

/*
* Top Ten Controller
*/
class Topten extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!$this->current_user->loggedin && CONFIG['MEMBERSONLY']) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    private function sql($limit = 10)
    {
        $_query = "SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data 
                  FROM torrents AS t 
                  LEFT JOIN peers AS p ON t.id = p.torrent 
                  WHERE p.seeder = 'no' 
                  GROUP BY t.id ORDER BY seeders + leechers DESC, seeders DESC, added ASC LIMIT $limit";
        $info['active'] = DB::run($_query)->fetchAll(); 
        $info['title1'] = "Top Most Active Torrents";
        
        $_query =  "SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data 
                    FROM torrents AS t 
                    LEFT JOIN peers AS p ON t.id = p.torrent 
                    WHERE p.seeder = 'no' 
                    GROUP BY t.id ORDER BY times_completed DESC LIMIT $limit";
        $info['snatch'] = DB::run($_query)->fetchAll();
        $info['title2'] = "Top Most Snatched Torrents";

        $_query = "SELECT name, flagpic, COUNT(users.country) as num 
                   FROM countries 
                   LEFT JOIN users ON users.country = countries.id 
                   GROUP BY name ORDER BY num DESC LIMIT $limit";
        $info['country'] = DB::run($_query)->fetchAll();
        $info['title3'] = "Top Countries";

        $_query = "SELECT id as userid, username, added, uploaded, downloaded, uploaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS upspeed, downloaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS downspeed 
                  FROM users
                  WHERE enabled = ?
                  ORDER BY uploaded DESC LIMIT $limit";
        $info['uploaders']= DB::run($_query, ['yes'])->fetchAll();
        $info['title4'] = "Top  Uploaders";

        $_query = "SELECT id as userid, username, added, uploaded, downloaded, uploaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS upspeed, downloaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS downspeed
                  FROM users 
                  WHERE enabled = ?
                  ORDER BY downloaded DESC LIMIT $limit";
        $info['downloaders'] = DB::run($_query, ['yes'])->fetchAll();
        $info['title5'] = "Top Downloaders";

        $_query = "SELECT id as userid, username, added, uploaded, downloaded, uploaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS upspeed, downloaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS downspeed
                  FROM users 
                  WHERE enabled = ?
                  ORDER BY downspeed DESC LIMIT $limit";
        $info['fastest'] = DB::run($_query, ['yes'])->fetchAll();
        $info['title6'] = "Top Fastest Downloaders";
        


        $_query = "SELECT id as userid, username, added, uploaded, downloaded, uploaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS upspeed, downloaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS downspeed 
                   FROM users 
                  WHERE enabled = ?
                  AND downloaded > 1073741824 ORDER BY uploaded / downloaded DESC LIMIT $limit";
        $info['bestsharer'] = DB::run($_query);
        $info['title7'] = "Top  Best Sharers";

        $_query = "SELECT id as userid, username, added, uploaded, downloaded, uploaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS upspeed, downloaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS downspeed 
                  FROM users 
                  WHERE enabled = ?
                  AND downloaded > 1073741824 ORDER BY uploaded / downloaded ASC, downloaded DESC LIMIT $limit";
        $info['worstshare'] = DB::run($_query)->fetchAll();
        $info['title8'] = "Top Worst Sharers";
        return $info;
    }
    
    // Top Torrents Default Page // todo
    public function index($limit = 10)
    {
        $data = [
            'TITLE' => LANG["TOP_10"],
            'V_info' => $this->sql($limit)
        ];
        
        // Load View
        $this->template('user-topten', $data, 'user');
    }

}