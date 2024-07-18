<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\CatagoriesModel;
use App\Models\TorrentsModel;
use App\Models\UsersModel;

/*
* Rss Controller
*/
class Rss 
{
    // RSS Default Page todo (simplify)
    public function index()
    {
        $USER = new User(0, 0);
        // Check User Input
        $passkey = $_GET["passkey"];
        $incldead = $_GET["incldead"];
        $cat = $_GET["cat"];
        $user = $_GET["user"];

        // Check User
        if ($passkey == ''){
            $passkey = $USER->has('passkey');
        }
        $member = UsersModel::fetchColumn('count(id)', ['passkey ='], [$passkey]);
        if ($member == 1) {
            $link = "$passkey";
        } else {
            $link = "";
        }
        
        if ($link == "" && CONFIG['MEMBERSONLY']) {
            Redirect(Url(), Txt("NO_PERMISSION"));
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
        if (Id($user)) {
            $wherea[] = "owner=$user";
        }
        if ($wherea) {
            $where = "WHERE " . implode(" AND ", $wherea);
        }
        $limit = "LIMIT 50";

        // Get Data
        $res = TorrentsModel::rssInfo($where, $limit);

        // start the RSS feed output
        header( "Content-type: text/xml; charset=".CHARSET."");
        echo "<?xml version='1.0' encoding='".CHARSET."'?>
        <rss version='2.0'>
        <channel>
        <title>" . htmlspecialchars(CONFIG['SITENAME']) . " | RSS</title>
        <link>https://github.com/M-jay84/M-BiTsy</link>
        <description>" . htmlspecialchars(CONFIG['SITENAME']) . " RSS</description>
        <language>en-us</language>";

        foreach ($res as $row) {
            $pubdate = date("r", sql_timestamp_to_unix_timestamp($row['added']));
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
        $USER = new User(0, 0);
        // Check User
        if (!$USER->permission('view_torrents')) {
            Redirect(Url(), Txt("NO_TORRENT_VIEW"));
        }

        // Init Data
        $data = [
            'V_title' => Txt("MAKE_RSS_FEED"),
            'V_cats' => CatagoriesModel::genreList(),
            'USER' => $USER
        ];

        // Load View
        template('Rss', $data, 'user');
    }

    // RSS Form Submit
    public function submit()
    {
        $USER = new User(0, 0);
        // Check User
        if (!$USER->permission('view_torrents')) {
            Redirect(Url(), Txt("NO_TORRENT_VIEW"));
        }

        if (!empty($_POST)) {
            // Sort Data
            $params = array();

            if ($USER->has('id')) {
                $params[] = "passkey=".$USER->has('passkey')."";
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
            if (Id($_POST['user'])) {
                $params[] = "user=$_POST[user]";
            }
            if ($params) {
                $param = "?" . implode("&amp;", $params);
            } else {
                $param = "";
            }
            
            Redirect(Url(), "Your RSS link is: <a href='".URLROOT."/rss$param'>".URLROOT."/rss$param</a>");

        } else {
            Redirect(Url(), Txt('NOTHING_SELECTED'));
        }
    }

}