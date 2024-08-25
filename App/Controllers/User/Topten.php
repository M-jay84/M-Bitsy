<?php

namespace App\Controllers\User;

use App\Libs\Controller;
use App\Libs\DB;
use App\Models\UsersModel;

/*
* Top Ten Controller
*/
class Topten extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
    }

    private function sql($limit = 10)
    {
        $info['active'] = DB::run("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' GROUP BY t.id ORDER BY seeders + leechers DESC, seeders DESC, added ASC LIMIT $limit")->fetchAll(); 
        $info['title1'] = "Top Most Active Torrents";
        $info['snatch'] = DB::run("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' GROUP BY t.id ORDER BY times_completed DESC LIMIT $limit")->fetchAll();
        $info['title2'] = "Top Most Snatched Torrents";
        $info['country'] = DB::run("SELECT name, flagpic, COUNT(users.country) as num 
		FROM countries LEFT JOIN users ON users.country = countries.id GROUP BY name ORDER BY num DESC LIMIT $limit")->fetchAll();
        $info['title3'] = "Top Countries";
        $info['uploaders']= UsersModel::fetchAll('id as userid, username, added, uploaded, downloaded, uploaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS upspeed, downloaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS downspeed',
                    ['enabled ='], ['yes'], "ORDER BY uploaded DESC LIMIT $limit");
        $info['title4'] = "Top  Uploaders";
        $info['downloaders'] = UsersModel::fetchAll('id as userid, username, added, uploaded, downloaded, uploaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS upspeed, downloaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS downspeed',
                   ['enabled ='], ['yes'], "ORDER BY downloaded DESC LIMIT $limit");
        $info['title5'] = "Top Downloaders";
        $info['fastest'] = UsersModel::fetchAll('id as userid, username, added, uploaded, downloaded, uploaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS upspeed, downloaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS downspeed',
                   ['enabled ='], ['yes'], "ORDER BY downspeed DESC LIMIT $limit");
        $info['title6'] = "Top Fastest Downloaders";
        $info['bestsharer'] = UsersModel::fetchAll('id as userid, username, added, uploaded, downloaded, uploaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS upspeed, downloaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS downspeed',
                   ['enabled ='], ['yes'], " AND downloaded > 1073741824 ORDER BY uploaded / downloaded DESC LIMIT $limit");
        $info['title7'] = "Top  Best Sharers";
        $info['worstshare'] = UsersModel::fetchAll('id as userid, username, added, uploaded, downloaded, uploaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS upspeed, downloaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS downspeed',
                   ['enabled ='], ['yes'], " AND downloaded > 1073741824 ORDER BY uploaded / downloaded ASC, downloaded DESC LIMIT $limit");
        $info['title8'] = "Top Worst Sharers";
        return $info;
    }
    
    // Top Torrents Default Page // todo
    public function index($limit = 10)
    {
    
        $info = $this->sql($limit);

        $data = [
            'page_title' => LANG["TOP_10"],
            'V_info' => $info
        ];
        
        // Load View
        $this->view('topten', $data, 'user');
    }

    // Top 100
    public function more($limit = 100)
    {
    
        $info = $this->sql($limit);
        
        require_once CONFIG['APPROOT']."/Views/inc/header.php";
        block_begin("Top Ten");

        block_end();
        require_once CONFIG['APPROOT']."/Views/inc/footer.php";
    }

}