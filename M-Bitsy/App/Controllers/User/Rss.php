<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\CatagoriesLogic;
use App\Models\RssLogic;

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
        // Get Data
        $res = RssLogic::rssInfo($this->current_user);

        // start the RSS feed output
        header( "Content-type: text/xml; charset=".CONFIG['CHARSET']."");
        echo "<?xml version='1.0' encoding='".CONFIG['CHARSET']."'?>
        <rss version='2.0'>
        <channel>
        <title>" . htmlspecialchars(CONFIG['SITENAME']) . " | RSS</title>
        <link>https://github.com/M-jay84/M-BiTsy</link>
        <description>" . htmlspecialchars(CONFIG['SITENAME']) . " RSS</description>
        <language>en-us</language>";

        foreach ($res['res'] as $row) {
            $pubdate = date("r", strtotime($row['added']));
            echo "<item>
                  <title>" . htmlspecialchars($row['name']) . "</title>
                  <guid>" . URLROOT . "/torrent/$row[id]/hit</guid>
                  <link>" . URLROOT . "/download/$row[id]/$res[link]</link>
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
            showMessage(URLROOT, LANG["NO_TORRENT_VIEW"]);
        }

        // Data For View
        $data = [
            'TITLE' => LANG["MAKE_RSS_FEED"],
            'CATAGORYS' => CatagoriesLogic::genreList()
        ];

        // Load View
        $this->template('user-rss', $data, 'user');
    }

    // RSS Form Submit
    public function submit()
    {
        // Check User
        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
            showMessage(URLROOT, LANG["NO_TORRENT_VIEW"]);
        }

        if (!empty($_POST)) {
            RssLogic::rsslink($this->current_user);
        } else {
            showMessage(URLROOT, LANG['NOTHING_SELECTED']);
        }
    }

}