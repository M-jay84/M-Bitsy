<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Libs\DB;
use App\Models\CatagoriesModel;
use App\Models\TorrentsModel;
use App\Models\UsersModel;

/*
* Rss Controller
*/
class Rss extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();
    }

    // RSS Default Page todo (simplify)
    public function index()
    {
        // Check User Input
        $passkey = $_GET["passkey"];
        $incldead = $_GET["incldead"];
        $cat = $_GET["cat"];
        $user = $_GET["user"];

        // Check User
        if ($passkey == ''){
            $passkey = $this->current_user->passkey;
        }
        $member = DB::fetchColumn('users', 'count(id)', ['passkey ='], [$passkey]);
        if ($member == 1) {
            $link = "$passkey";
        } else {
            $link = "";
        }
        
        if ($link == "" && CONFIG['MEMBERSONLY']) {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }

        // Sort Input
        $where = "";
        $wherea = array();
        if (!$incldead) {
            $wherea[] = "visible='yes'";
        }
        if ($cat) {
            $cats = implode(", ", array_unique(array_map("intval", explode(",", (string) $cat))));
            $wherea[] = "category in ($cats)";
        }
        if (intval($user)) {
            $wherea[] = "owner=$user";
        }
        if ($wherea) {
            $where = "WHERE " . implode(" AND ", $wherea);
        }
        $limit = "LIMIT 50";

        // Get Data
        $res = TorrentsModel::rssInfo($where, $limit);

        // start the RSS feed output
        header( "Content-type: text/xml; charset=".CONFIG['CHARSET']."");
        echo "<?xml version='1.0' encoding='".CONFIG['CHARSET']."'?>
        <rss version='2.0'>
        <channel>
        <title>" . htmlspecialchars(CONFIG['SITENAME']) . " | RSS</title>
        <link>https://github.com/M-jay84/M-BiTsy</link>
        <description>" . htmlspecialchars(CONFIG['SITENAME']) . " RSS</description>
        <language>en-us</language>";

        foreach ($res as $row) {
            $pubdate = date("r", strtotime($row['added']));
            echo "<item>
                  <title>" . htmlspecialchars($row['name']) . "</title>
                  <guid>" . URLROOT . "/torrent/$row[id]/hit</guid>
                  <link>" . URLROOT . "/download/$row[id]/$link</link>
                  <pubDate>" . $pubdate . "</pubDate>
                  <category> " . $row["cat_parent"] . ": " . $row["cat_name"] . "</category>
                  <description>Size: " . mksize($row['size']) . " Seeders: " . $row['seeders'] . " Leechers: " . $row['leechers'] . "</description>
                  </item>";
        }
        echo ("</channel></rss>");
    }

    // RSS Custom Default Page
    public function custom()
    {
        // Check User
        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
            $this->showMessage(URLROOT, LANG["NO_TORRENT_VIEW"]);
        }

        // Data For View
        $data = [
            'page_title' => LANG["MAKE_RSS_FEED"],
            'V_cats' => CatagoriesModel::genreList()
        ];

        // Load View
        $this->view('user-Rss', $data, 'user');
    }

    // RSS Form Submit
    public function submit()
    {
        // Check User
        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
            $this->showMessage(URLROOT, LANG["NO_TORRENT_VIEW"]);
        }

        if (!empty($_POST)) {
            // Sort Data
            $params = array();

            if ($this->current_user->id) {
                $params[] = "passkey=".$this->current_user->passkey."";
            }
            if ($cats = $_POST["cats"]) {
                $catlist = array();
                foreach ($cats as $cat) {
                    if (is_numeric($cat)) {
                        $catlist[] = $cat;
                    }
                }
                if ($catlist) {
                    $params[] = "cat=" . implode(",", $catlist);
                }
            }
            if ($_POST["incldead"]) {
                $params[] = "incldead=1";
            }
            if (intval($_POST['user'])) {
                $params[] = "user=$_POST[user]";
            }
            if ($params) {
                $param = "?" . implode("&amp;", $params);
            } else {
                $param = "";
            }
            
            $this->showMessage(URLROOT, "Your RSS link is: <a href='".URLROOT."/rss$param'>".URLROOT."/rss$param</a>");

        } else {
            $this->showMessage(URLROOT, LANG['NOTHING_SELECTED']);
        }
    }

}