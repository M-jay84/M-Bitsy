<?php
require_once "assets/themes/" . (isset($_SESSION['stylesheet']) ? $_SESSION['stylesheet'] : CONFIG['DEFAULTTHEME']) . "/header.php";
require APPROOT."/Views/home/hitnrun.php";
require APPROOT."/Views/home/notice.php";
require APPROOT."/Views/home/news.php";
require APPROOT."/Views/home/shoutbox.php";
require APPROOT."/Views/home/lastforumpost.php";
require APPROOT."/Views/home/forum.php";
require APPROOT."/Views/home/carousel.php";
require APPROOT."/Views/home/grid.php";
require APPROOT."/Views/home/torrent.php";
require APPROOT."/Views/home/visitedusers.php";
require APPROOT."/Views/home/disclaimer.php";
column('middle', $data['USER']);
require_once "assets/themes/" . (isset($_SESSION['stylesheet']) ? $_SESSION['stylesheet'] : CONFIG['DEFAULTTHEME']) . "/footer.php";