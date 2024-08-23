<?php

use App\Libs\DB;

// Function That Returns The Health Level Of A Torrent
function health($leechers, $seeders)
{
    if (($leechers == 0 && $seeders == 0) || ($leechers > 0 && $seeders == 0)) {
        return 0;
    } elseif ($seeders > $leechers) {
        return 10;
    }
    $ratio = $seeders / $leechers * 100;
    if ($ratio > 0 && $ratio < 15) {
        return 1;
    } elseif ($ratio >= 15 && $ratio < 25) {
        return 2;
    } elseif ($ratio >= 25 && $ratio < 35) {
        return 3;
    } elseif ($ratio >= 35 && $ratio < 45) {
        return 4;
    } elseif ($ratio >= 45 && $ratio < 55) {
        return 5;
    } elseif ($ratio >= 55 && $ratio < 65) {
        return 6;
    } elseif ($ratio >= 65 && $ratio < 75) {
        return 7;
    } elseif ($ratio >= 75 && $ratio < 85) {
        return 8;
    } elseif ($ratio >= 85 && $ratio < 95) {
        return 9;
    } else {
        return 10;
    }
}

// Return Tags
function get_tags($id) 
{
    $tag =  DB::fetchRow('torrenttag', 'type1, type2, type3', ['torrentid ='], [$id]);

    $tags = '';
    if (!empty($tag['type1'])) {
        $tags .= "&nbsp;<a href=".CONFIG['URLROOT']."/tag/$tag[type1]><span class='badge border ttbtn'>$tag[type1]</span></a>&nbsp;";
    }
    if (!empty($tag['type2'])) {
        $tags .= "<a href=".CONFIG['URLROOT']."/tag/$tag[type2]><span class='badge border ttbtn'>$tag[type2]</span></a>&nbsp;";
    }
    if (!empty($tag['type3'])) {
        $tags .= "<a href=".CONFIG['URLROOT']."/tag/$tag[type3]><span class='badge border ttbtn'>$tag[type3]</span></a>&nbsp;";
    }
    
    return $tags;
}

// Function that assigns a color based on the value of the ratio
function get_ratio_color($ratio)
{
   if ($ratio == '---') {
       return "<font color='FFFFFF'>$ratio</font>";
   }
   if ($ratio < 0.5) {
       return "<font color='#fc0313'>$ratio</font>";
   }
   if ($ratio < 1) {
       return "<font color='#fc6203'>$ratio</font>";
   }
   if ($ratio < 1.5) {
       return "<font color='#ebc334'>$ratio</font>";
   }
   if ($ratio < 2) {
       return "<font color='#1a7d09'>$ratio</font>";
   }
   return "<font color='#0d6efd'>$ratio</font>";
}

// Function That Returns The Image Corresponding To The Votes
function ratingpic($num)
{
    $r = round($num * 2) / 2;
    if ($r != $num) {
        $n = $num - $r;
        if ($n < .25) {
            $n = 0;
        } elseif ($n >= .25 && $n < .75) {
            $n = .5;
        }

        $r += $n;
    }
    if ($r < 1 || $r > 5) {
        return;
    }

    return "<img src=\"" . CONFIG['URLROOT'] . "/assets/images/rating/$r.webp\" border=\"0\" alt=\"rating: $num/5\" title=\"rating: $num/5\" />";
}

// Sort Torrent Table By Column
function sortMod()
{
    $sort = $_GET['sort'] ?? '';
    $order = $_GET['order'] ?? '';
    switch ($sort) {
        case 'id':$column = "id";
            break;
        case 'name':$column = "name";
            break;
        case 'comments':$column = "comments";
            break;
        case 'size':$column = "size";
            break;
        case 'times_completed':$column = "times_completed";
            break;
        case 'seeders':$column = "seeders";
            break;
        case 'leechers':$column = "leechers";
            break;
        case 'category':$column = "category";
            break;
        default:$column = "id";
            break;
    }

    switch ($order) {
        case 'asc':$ascdesc = "ASC";
            break;
        case 'desc':$ascdesc = "DESC";
            break;
        default:$ascdesc = "DESC";
            break;
    }

    $orderby = 'ORDER BY torrents.' . $column . ' ' . $ascdesc;
    $pagerlink = "sort=" . $column . "&amp;order=" . strtolower($ascdesc) . "&amp;";

    return [
        'orderby' => $orderby, 'pagerlink' => $pagerlink,
        'column' => $column, 'by' => $ascdesc,
    ];
}

// Torrent Search
function search()
{
    $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
    $cats = (int) isset($_GET['cat']) ? $_GET['cat'] : 0;
    $incldead = (int) isset($_GET['incldead']) ? $_GET['incldead'] : 0;
    $freeleech = (int) isset($_GET['freeleech']) ? $_GET['freeleech'] : 0;
    $inclexternal = (int) isset($_GET['inclexternal']) ? $_GET['inclexternal'] : 0;
    $lang = (int) isset($_GET['language']) ? $_GET['language'] : 0;
    
    $url = "?"; // assign url
    $wherea = []; // assign conditions
    $params = []; // assign vars
    // browse
    $cats = (int) (isset($_GET['cat'])) ? $_GET['cat'] : 0;
    $parent_cat = (isset($_GET['parent_cat'])) ? $_GET['parent_cat'] : 0;

    if (!$keyword == '') {
        $keys = explode(" ", $keyword);
        foreach ($keys as $k) {
            $ssa[] = " torrents.name LIKE '%$k%' ";
        }
        $wherea[] = '(' . implode(' OR ', $ssa) . ')';
        $url .= "keyword=" . urlencode($keyword) . "&";
    }

    if (!$cats == 0) {
        $wherea[] = "category = $cats";
        $url .= "cat=" . urlencode($cats) . "&";
    }

    if (!$parent_cat == 0) {
        $wherea[] = "categories.parent_cat = $cats";
        $url .= "parent_cat=" . urlencode($cats) . "&";
    }

    if ($incldead == 1) {
        $url .= "incldead=1&";
    } elseif ($incldead == 2) {
        $params[] = 'no';
        $wherea[] = "visible = ?";
        $url .= "incldead=2&";
    } else {
        $params[] = 'yes';
        $wherea[] = "visible = ?";
    }

    if ($freeleech == 1) {
        $params[] = 0;
        $wherea[] = "freeleech = ?";
        $url .= "freeleech=1&";
    } elseif ($freeleech == 2) {
        $params[] = 1;
        $wherea[] = "freeleech = ?";
        $url .= "freeleech=2&";
    }

    if ($inclexternal == 1) {
        $params[] = 'no';
        $wherea[] = "external = ?";
        $url .= "inclexternal=1&";
    } elseif ($inclexternal == 2) {
        $params[] = 'yes';
        $wherea[] = "external = ?";
        $url .= "inclexternal=2&";
    }

    if ($lang) {
        $params[] = $lang;
        $wherea[] = "torrentlang = ?";
        $url .= "lang=" . urlencode($lang) . "&";
    }

    $where = implode(' AND ', $wherea);

    // browse
    $wherecatina = array();
    $wherecatin = "";
    $res = DB::fetchAll('categories', 'id', '');
    foreach ($res as $row) {
        if (isset($_GET["c$row[id]"])) {
            $wherecatina[] = $row["id"];
            $url .= "c$row[id]=1&";
        }
        $wherecatin = implode(", ", $wherecatina);
    }

    if ($wherecatin) {
        $where .= ($where ? " AND " : "") . "category IN(" . $wherecatin . ")";
    }

    if ($where != '') {
        $where = 'WHERE ' . $where;
    }

    $sortmod = sortMod();
    $orderby = 'ORDER BY torrents.' . $sortmod['column'] . ' ' . $sortmod['by'];
    $pagerlink = $sortmod['pagerlink'];

    $array = [
        'where'=>$where,
        'keyword'=>$keyword,
        'orderby'=>$orderby,
        'params'=>$params,
        'url'=>$url,
        'pagerlink'=>$pagerlink,
        'parent_cat' => $parent_cat,
        'wherecatina' => $wherecatina,
    ];
    return $array;
}

// Torrent Search
function torrentsearch($searchwhat)
{
    $search = $_GET['search'] ?? '';
    $letter = $_GET["letter"] ?? '';
    $url = "?";

    if ($search != '') {
        $keys = explode(" ", $search);
        foreach ($keys as $k) {
            $ssa[] = " $searchwhat LIKE '%$k%' ";
        }
        $query = '(' . implode(' OR ', $ssa) . ')';
        $url .= "search=" . urlencode($search);
    } else {
        if (strlen($letter) > 1) {
            die;
        }
        if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz", $letter) === false) {
            return false;
        }
        $query = "$searchwhat LIKE '$letter%'";
        $url = "?letter=$letter";
    }

    $array = [
        'url' => $url,
        'query' => $query
    ];

    return $array;
}

// Get Torrent Files
function gettorrentfiles()
{
    $files = array();
    $dh = opendir(CONFIG['UPLOADDIR']."/import/");
    while (false !== ($file = readdir($dh))) {
        if (preg_match("/\.torrent$/i", $file)) {
            $files[] = $file;
        }
    }
    closedir($dh);
    return $files;
}

// Post Anon
function anon($data)
{
    if ($data["anon"] == "yes" && !$data['owned']) {
        echo LANG["ANON"];
    } elseif ($data["username"]) { ?>
        <a href='profile/<?php echo $data["owner"]; ?>'><?= coloredname($data["owner"]) ?></a> <?php
    } else { ?>
        <?= LANG["SYSTEM"] ?> <?php
    }
}

// Get Magnet Link
function magnetlink($data)
{
    if (!CONFIG['FORCETHANKS'] && $data["external"] == 'yes' || $data["external"] == 'yes' && $data["vip"] == 'yes' && $_SESSION['class'] >= CONFIG['_VIP']) {
        print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $data["info_hash"] . "&dn=" . $data["filename"] . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><i class='fa fa-magnet fa-1x' style='color:green' title='" . LANG["DOWNLOADED"] . "'></i></a>&nbsp;");
    } else {
        $like = DB::fetchRow('likes', ['liked =', 'type =', 'user ='], [$data['id'], 'torrent', $_SESSION['id']]);
        if ($like) {
            if ($data["external"] == 'yes') {
                if ($_SESSION['can_download'] == 'yes') {
                    print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $data["info_hash"] . "&dn=" . $data["filename"] . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><i class='fa fa-magnet fa-1x' style='color:green' title='" . LANG["DOWNLOADED"] . "'></i></a>&nbsp;");
                }
            }
        } else {
            if ($_SESSION['id'] != $data["owner"]) {
                print("<a href='" . CONFIG['URLROOT'] . "/like/$data[stmt][id]/torrent'><button  class='btn btn-sm ttbtn'>" . LANG["THANKS"] . "</button></a><br>");
            } elseif ($data["vip"] == 'yes' && $_SESSION['class'] <= CONFIG['_VIP']) {

            } elseif ($data["external"] == 'yes') {
                print("&nbsp;<a href=\"magnet:?xt=urn:btih:" . $data["info_hash"] . "&dn=" . $data["filename"] . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><i class='fa fa-magnet fa-1x' style='color:green' title='" . LANG["DOWNLOADED"] . "'></i></a>&nbsp;");
            }
        }
    }
}

// TRANSLATE NFO BLOCK
function my_nfo_translate($nfo)
{
    $trans = array(
        "\x80" => "&#199;", "\x81" => "&#252;", "\x82" => "&#233;", "\x83" => "&#226;", "\x84" => "&#228;", "\x85" => "&#224;", "\x86" => "&#229;", "\x87" => "&#231;", "\x88" => "&#234;", "\x89" => "&#235;", "\x8a" => "&#232;", "\x8b" => "&#239;", "\x8c" => "&#238;", "\x8d" => "&#236;", "\x8e" => "&#196;", "\x8f" => "&#197;", "\x90" => "&#201;",
        "\x91" => "&#230;", "\x92" => "&#198;", "\x93" => "&#244;", "\x94" => "&#246;", "\x95" => "&#242;", "\x96" => "&#251;", "\x97" => "&#249;", "\x98" => "&#255;", "\x99" => "&#214;", "\x9a" => "&#220;", "\x9b" => "&#162;", "\x9c" => "&#163;", "\x9d" => "&#165;", "\x9e" => "&#8359;", "\x9f" => "&#402;", "\xa0" => "&#225;", "\xa1" => "&#237;",
        "\xa2" => "&#243;", "\xa3" => "&#250;", "\xa4" => "&#241;", "\xa5" => "&#209;", "\xa6" => "&#170;", "\xa7" => "&#186;", "\xa8" => "&#191;", "\xa9" => "&#8976;", "\xaa" => "&#172;", "\xab" => "&#189;", "\xac" => "&#188;", "\xad" => "&#161;", "\xae" => "&#171;", "\xaf" => "&#187;", "\xb0" => "&#9617;", "\xb1" => "&#9618;", "\xb2" => "&#9619;",
        "\xb3" => "&#9474;", "\xb4" => "&#9508;", "\xb5" => "&#9569;", "\xb6" => "&#9570;", "\xb7" => "&#9558;", "\xb8" => "&#9557;", "\xb9" => "&#9571;", "\xba" => "&#9553;", "\xbb" => "&#9559;", "\xbc" => "&#9565;", "\xbd" => "&#9564;", "\xbe" => "&#9563;", "\xbf" => "&#9488;", "\xc0" => "&#9492;", "\xc1" => "&#9524;", "\xc2" => "&#9516;", "\xc3" => "&#9500;",
        "\xc4" => "&#9472;", "\xc5" => "&#9532;", "\xc6" => "&#9566;", "\xc7" => "&#9567;", "\xc8" => "&#9562;", "\xc9" => "&#9556;", "\xca" => "&#9577;", "\xcb" => "&#9574;", "\xcc" => "&#9568;", "\xcd" => "&#9552;", "\xce" => "&#9580;", "\xcf" => "&#9575;", "\xd0" => "&#9576;", "\xd1" => "&#9572;", "\xd2" => "&#9573;", "\xd3" => "&#9561;", "\xd4" => "&#9560;",
        "\xd5" => "&#9554;", "\xd6" => "&#9555;", "\xd7" => "&#9579;", "\xd8" => "&#9578;", "\xd9" => "&#9496;", "\xda" => "&#9484;", "\xdb" => "&#9608;", "\xdc" => "&#9604;", "\xdd" => "&#9612;", "\xde" => "&#9616;", "\xdf" => "&#9600;", "\xe0" => "&#945;", "\xe1" => "&#223;", "\xe2" => "&#915;", "\xe3" => "&#960;", "\xe4" => "&#931;", "\xe5" => "&#963;",
        "\xe6" => "&#181;", "\xe7" => "&#964;", "\xe8" => "&#934;", "\xe9" => "&#920;", "\xea" => "&#937;", "\xeb" => "&#948;", "\xec" => "&#8734;", "\xed" => "&#966;", "\xee" => "&#949;", "\xef" => "&#8745;", "\xf0" => "&#8801;", "\xf1" => "&#177;", "\xf2" => "&#8805;", "\xf3" => "&#8804;", "\xf4" => "&#8992;", "\xf5" => "&#8993;", "\xf6" => "&#247;",
        "\xf7" => "&#8776;", "\xf8" => "&#176;", "\xf9" => "&#8729;", "\xfa" => "&#183;", "\xfb" => "&#8730;", "\xfc" => "&#8319;", "\xfd" => "&#178;", "\xfe" => "&#9632;", "\xff" => "&#160;",
    );
    $trans2 = array("\xe4" => "&auml;", "\xF6" => "&ouml;", "\xFC" => "&uuml;", "\xC4" => "&Auml;", "\xD6" => "&Ouml;", "\xDC" => "&Uuml;", "\xDF" => "&szlig;");
    $all_chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $last_was_ascii = false;
    $tmp = "";
    $nfo = $nfo . "\00";
    for ($i = 0; $i < (strlen($nfo) - 1); $i++) {
        $char = $nfo[$i];
        if (isset($trans2[$char]) and ($last_was_ascii or strpos($all_chars, ($nfo[$i + 1])))) {
            $tmp = $tmp . $trans2[$char];
            $last_was_ascii = true;
        } else {
            if (isset($trans[$char])) {
                $tmp = $tmp . $trans[$char];
            } else {
                $tmp = $tmp . $char;
            }
            $last_was_ascii = strpos($all_chars, $char);
        }
    }
    return $tmp;
}

// DISPLAY NFO BLOCK
function displaynfo($id)
{
    $nfofilelocation = CONFIG['UPLOADDIR'] . "/nfos/$id.nfo";
    $filegetcontents = file_get_contents($nfofilelocation);
    $nfo = $filegetcontents;
    if ($nfo) {
        $nfo = my_nfo_translate($nfo);
        echo "<p class='text-center'><b>NFO:</b></p>";
        print("<textarea class='form-control' style='min-width:700px;height:700px;' readonly='readonly'>" . stripslashes($nfo) . "</textarea>");
    } else {
        print(LANG["NO_NFO_ERROR"] );
    }
}