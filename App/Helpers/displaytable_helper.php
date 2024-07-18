<?php

use App\Libs\MDBS;
use App\Models\LikesModel;
use App\Models\PeersModel;

// Function For Comment Table
function commenttable($USER, $res, $type = null)
{
    while ($row = $res->fetch(PDO::FETCH_LAZY)) {
        $postername = coloredname($row["user"]);
        if ($postername == "System") {
            $title = "";
            $avatar = "";
            $usersignature = "";
            $userdownloaded = "";
            $useruploaded = "";
        } else {
            $privacylevel = $row["privacy"];
            $avatar = htmlspecialchars($row["avatar"]);
            $title = format_comment($row["title"]);
            $usersignature = stripslashes(format_comment($row["signature"]));
            $userdownloaded = mksize($row["downloaded"]);
            $useruploaded = mksize($row["uploaded"]);
        }

        $userratio = $row["downloaded"] > 0 ? number_format($row["uploaded"] / $row["downloaded"]) : "---";

        if (!$avatar) {
            $avatar = URLROOT . "/assets/images/misc/default_avatar.webp";
        }

        $commenttext = format_comment($row["text"]);

        $edit = null;
        if ($type == "torrent" && $USER->permission('edit_torrents') || $type == "news"  || $type == "req" && $USER->permission('edit_news') || $USER->has('id') == $row['user'] && $USER->has('id')) {
            $edit = '<a href="#" data-bs-toggle="modal" data-bs-target="#editModal'.$row['id'].'"><i class="fa fa-pencil" title="'. Txt("EDIT") .'"></i></a>';
        }

        $delete = null;
        if ($type == "torrent" && $USER->permission('delete_torrents') || $type == "req" || $type == "id" || $type == "news" && $USER->permission('delete_news')) {
            $delete = '<a href=' . URLROOT . '/comment/delete/' . $row['id'] . '><i class="fa fa-trash" title="'. Txt("DEL") .'"></i></a>';
        }

        ?>
        <div class='row p-4'>
            <div class="col-2">
                <?php if (!$USER->permission('edit_users') && $privacylevel == 'strong') : ?>
                    <a href="<?= URLROOT ?>/profile/<?= $row['id'] ?>"><b><?= $postername ?></b></a>
                    <?= $title ?><br>
                    <img width="80" height="80" src="<?= $avatar ?>" alt="" /><br>
                    <i class="fa fa-upload" title="upload"></i> <?= $useruploaded ?><br />
                    <i class="fa fa-download" title="download"></i> <?= $userdownloaded ?><br />
                    <?= Txt("RATIO") ?>: <?= get_ratio_color($userratio) ?><br />
                <?php else : ?>
                    <a href="<?= URLROOT ?>/profile/<?= $row['id'] ?>"><b><?= Txt('PRIVATE') ?></b></a>
                    <?= $title ?><br>
                    <img width="80" height="80" src="<?= URLROOT ?>/assets/images/misc/default_avatar.webp" alt="" /><br>
                    <i class="fa fa-upload" title="upload"></i> <?= $useruploaded ?><br />
                    <i class="fa fa-download" title="download"></i> <?= $userdownloaded ?><br />
                    <?= Txt("RATIO") ?>: <?= get_ratio_color($userratio) ?><br />
                <?php endif; ?>
            </div>
            <div class="col-10">
                <?= Txt("POSTED") ?>: <?php echo date("d-m-Y \\a\\t H:i:s", utc_to_tz_time($row["added"])) ?> <a id="comment<?= $row["id"] ?>"></a><br>
                <div class="p-2"><?= $commenttext ?></div>
                <hr>
                <?= $usersignature ?>
            </div>
            <div class="col-12">
                <?= $edit ?> <?= $delete ?> <a href="#" data-bs-toggle="modal" data-bs-target="#reportModal"><i class="fa fa-flag" title="<?= Txt("REPORT") ?>"></i></a>
            </div>
        </div>

        <!-- Edit Modal -->
        <form id="submit" method="post" action="<?= URLROOT ?>/comment/submit/<?= $row['id'] ?>/<?= $type ?>">
            <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
                    <div class="modal-content">
                        <div class="modal-body">
                            <input type='hidden' name="comment_id" value='<?= $row["id"] ?>' />
                            <?php echo textbbcode("submit", "text", $row["text"]); ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= Txt("CLOSE") ?></button>
                            <button type="submit" class="btn btn-sm ttbtn"><?= Txt("SAVE") ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Report Modal -->
        <form id="report" method="post" action="<?= Url('report') ?>">
            <div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
                    <div class="modal-content">
                        <div class="modal-body">
                            <p class="text-center"><?= Txt("ARE_YOU_SURE_REPORT") ?> comment.</p>
                            <input type='hidden' name='id' value='<?= $row["id"] ?>' />
                            <input type='hidden' name='postid' value='<?= $row["text"] ?>' />
                            <input type='hidden' name='type' value='<?= $type ?>' />
                            <p class="text-center"><?= Txt("REASON") ?>.</p>
                            <input class="form-control fullwidth" type='text' name='reason' /><br>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= Txt("CLOSE") ?></button>
                            <button type="submit" class="btn btn-sm ttbtn"><?= Txt("SAVE") ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
    }
}


// Create Table Of Peers
function peerstable($res)
{
    $html = "<div class='table-responsive p-3'>";
    $html .= "<table class='table table-striped'>";
    $html .= "<thead>";
    $html .= "<tr>";
    $html .= "<th>" . Txt("NAME") . "</th>";
    $html .= "<th>" . Txt("SIZE") . "</th>";
    $html .= "<th>" . Txt("UPLOADED") . "</th>";
    $html .= "<th>" . Txt("DOWNLOADED") . "</th>";
    $html .= "<th>" . Txt("RATIO") . "</th>";
    $html .= "</tr>";
    $html .= "</thead>";
    $html .= "<tbody>";
    foreach ($res as $arr) {
        $userratio = $arr["downloaded"] > 0 ? number_format($arr["uploaded"] / $arr["downloaded"], 1) : "---";
        $html .= "<tr>";
        $html .= "<td><a href=" . URLROOT . "/torrent/$arr[torrent]/hit'><b>" . htmlspecialchars($arr["name"]) . "</b></a></td>";
        $html .= "<td>" . mksize($arr["size"]) . "</td>";
        $html .= "<td>" . mksize($arr["uploaded"]) . "</td>";
        $html .= "<td>" . mksize($arr["downloaded"]) . "</td>";
        $html .= "<td>".get_ratio_color($userratio)."</td>";
        $html .= "</tr>";
    }
    $html .= "</tbody>";
    $html .= "</table>";
    $html .= "</div>";
    return $html;
}

// Create Table From Snatched
function snatchtable($res, $col)
{
    $html = "<div class='table-responsive p-3'>";
    $html .= "<table class='table table-striped'>";
    $html .= "<thead>";
    $html .= "<tr>";
    $html .= "<th>" . Txt("NAME") . "</th>";
    $html .= "<th>" . Txt("UPLOADED") . "</th>";
    $html .= "<th>" . Txt("DOWNLOADED") . "</th>";
    $html .= "<th>" . Txt("RATIO") . "</th>";
    $html .= "<th>" . Txt("ADDED") . "</th>";
    $html .= "<th>" . Txt("LAST_ACTION") . "</th>";
    $html .= "<th>" . Txt("SEED_TIMED") . "</th>";
    $html .= "<th>" . Txt("COMPLETED") . "</th>";
    $html .= "<th>" . Txt("SEEDING") . "</th>";
    $html .= "<th>" . Txt("HNR") . "</th>";
    $html .= "</tr>";
    $html .= "</thead>";
    $html .= "<tbody>";
    foreach ($res as $row) {
        $userratio = $row['download'] > 0 ? number_format($row['upload'] / $row['download'], 1) : "---";
        $complete = $row['completed'] != 'yes' ? "<font color=#27B500><b>" . Txt("YES") . "</b></font>" : "<font color=#FF1200><b>" . Txt("NO") . "</b></font>";
        $seeding = $row['seeding'] != 'yes' ? "<font color=#27B500><b>" . Txt("YES") . "</b></font>" : "<font color=#FF1200><b>" . Txt("NO") . "</b></font>";
        $hnr = $row['hnr'] != 'yes' ? "<font color=#27B500><b>" . Txt("YES") . "</b></font>" : "<font color=#FF1200><b>" . Txt("NO") . "</b></font>";
        $seedtime = $row['last_time'] ? mkprettytime($row['last_time']) : '---';
        $name = $col == 'torrent' ?  coloredname($row['id']) : htmlspecialchars(CutName($row['name'], 35));
        $html .= "<tr>";
        $html .= "<td><b>$name</b></td>";
        $html .= "<td><font color='#27B500'>". mksize($row['upload']) ."</font></td>";
        $html .= "<td><font color='#FF1200'>". mksize($row['download']) ."</font></td>";
        $html .= "<td>".get_ratio_color($userratio)."</td>";
        $html .= "<td>".date('d.M.Y H:i', sql_timestamp_to_unix_timestamp(utc_to_tz(get_date_time($row['start_time']))))."</td>";
        $html .= "<td>".date('d.M.Y H:i', sql_timestamp_to_unix_timestamp(utc_to_tz(get_date_time($row['upload_time']))))."</td>";
        $html .= "<td>$seedtime</td>";
        $html .= "<td>".$complete."</td>";
        $html .= "<td>".$seeding."</td>";
        $html .= "<td>".$hnr."</td>";
        $html .= "</tr>";
    }
    $html .= "</tbody>";
    $html .= "</table>";
    $html .= "</div>";
    return $html;
}

// Function To Display Tables Of Torrents
function torrenttable($USER, $res, $columns = TORRENTTABLE_COLUMNS)
{
    // Set Waiting Time
    if (CONFIG['MEMBERSONLY_WAIT'] && CONFIG['MEMBERSONLY'] && in_array($USER->has('class'), explode(",", CONFIG['CLASS_WAIT']))) {
        $gigs = $USER->has('uploaded') / (1024 * 1024 * 1024);
        $userratio = $USER->has('downloaded') > 0 ? number_format($USER->has('uploaded') / $USER->has('downloaded'), 1) : "---";
        if ($userratio < 0 || $gigs < 0) {
            $wait = CONFIG['A_WAIT'];
        } elseif ($userratio < CONFIG['RATIOA'] || $gigs < CONFIG['GIGSA']) {
            $wait = CONFIG['A_WAIT'];
        } elseif ($userratio < CONFIG['RATIOB'] || $gigs < CONFIG['GIGSB']) {
            $wait = CONFIG['B_WAIT'];
        } elseif ($userratio < CONFIG['RATIOC'] || $gigs < CONFIG['GIGSC']) {
            $wait = CONFIG['C_WAIT'];
        } elseif ($userratio < CONFIG['RATIOD'] || $gigs < CONFIG['GIGSD']) {
            $wait = CONFIG['D_WAIT'];
        } else {
            $wait = 0;
        }
    }
    $wait = '';
    // Columns
    $cols = explode(",", $columns);
    $cols = array_map("strtolower", $cols);
    $cols = array_map("trim", $cols);
    $colspan = count($cols);
    // Start The Table
    echo '<div class="table-responsive"><table class="table table-striped"><thead><tr>';

    foreach ($cols as $col) {
        switch ($col) {
            case 'category':
                echo "<th>" . Txt("TYPE") . "</th>";
                break;
            case 'name':
                echo "<th>" . Txt("NAME") . "</th>";
                break;
            case 'dl':
                echo "<th>" . Txt("DL") . "</th>";
                break;
            case 'magnet':
                echo "<th>" . Txt("MAGNET") . "</th>";
                break;
            case 'uploader':
                echo "<th>" . Txt("UPLOADER") . "</th>";
                break;
            case 'tube':
                echo "<th>" . Txt("YOUTUBE") . "</th>";
                break;
            case 'tmdb':
                echo "<th>" . Txt("MDB") . "</th>";
                break;
            case 'comments':
                echo "<th>" . Txt("COMM") . "</th>";
                break;
            case 'nfo':
                echo "<th>" . Txt("NFO") . "</th>";
                break;
            case 'size':
                echo "<th>" . Txt("SIZE") . "</th>";
                break;
            case 'completed':
                echo "<th>" . Txt("C") . "</th>";
                break;
            case 'seeders':
                echo "<th>" . Txt("S") . "</th>";
                break;
            case 'leechers':
                echo "<th>" . Txt("L") . "</th>";
                break;
            case 'health':
                echo "<th>" . Txt("HEALTH") . "</th>";
                break;
            case 'external':
                if (CONFIG['ALLOWEXTERNAL']) {
                    echo "<th>" . Txt("L/E") . "</th>";
                }
                break;
            case 'added':
                echo "<th>" . Txt("ADDED") . "</th>";
                break;
            case 'speed':
                echo "<th>" . Txt("SPEED") . "</th>";
                break;
            case 'wait':
                if ($wait) {
                    echo "<th>" . Txt("WAIT") . "</th>";
                }
                break;
            case 'rating':
                echo "<th>" . Txt("RATINGS") . "</th>";
                break;
            case 'bookmark':
                echo "<th>" . Txt("DEL") . "&nbsp;<input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>";
                break;
            case 'admin':
                if ($USER->has('class') == 7) {
                    echo "<th>" . Txt("STAFF") . "</th>";
                }
                break;
        }
    }
    // Do They Have To Wiait
    if ($wait && !in_array("wait", $cols)) {
        echo "<th>" . Txt("WAIT") . "</th>";
    }

    echo "</tr></thead>";

    foreach ($res as $row) {
        $id = $row["id"];
        print("<tr>\n");

        foreach ($cols as $col) {
            switch ($col) {
                case 'category':
                    print("<td>");
                    if (!empty($row["cat_name"])) {
                        print("<center><a href=\"" . URLROOT . "/browse?cat=" . $row["category"] . "\">");
                        if (!empty($row["cat_pic"]) && $row["cat_pic"] != "") {
                            print("<i class='fa ".$row['cat_pic']."' style='font-size:22px;' title='".$row["cat_name"]."'>  </i>");
                        } else {
                            print($row["cat_parent"] . ": " . $row["cat_name"]);
                        }
                        print("</a></center>");
                    } else {
                        print("-");
                    }
                    print("</td>\n");
                    break;
                case 'name':
                    $char1 = 40; //cut name length
                    $smallname = htmlspecialchars(CutName($row["name"], $char1));
                    $dispname = "<b>" . $smallname . "</b>";
                    $added = date("M d, Y", utc_to_tz_time($row['added']));
                    $type ='';
                    if (strtotime($added) >= strtotime("yesterday")){ // Only show for torrents 1 day old
                        $type .= "<b><font color='#ff0000'> (" . Txt("NEW") . "!) </font></b>";
                    }
                    if ($row["freeleech"] == 1) {
                        $type .= " <i class='fa fa-sun-o' title='" . Txt("FREELEECH") . "'></i>";
                    }
                    if ($row["vip"] == "yes") {
                        $type .= " <i class='fa fa-user-secret' title='" . Txt("VIP") . "'></i>";
                    }
                    if ($row["sticky"] == "yes") {
                        $type .= " <i class='fa fa-thumb-tack' title='" . Txt("STICKY") . "'></i>";
                    }
                    //print("<td class='ttable_col$x' nowrap='nowrap'><a href=\"" . URLROOT . "/torrent/$id/hit\">$dispname</a></td>");
                    // BALLOON TOOLTIP MOD
                    print("<td nowrap='nowrap'><a href=\"" . URLROOT . "/torrent/$id/hit\" onMouseover=\"return overlib('<table class=ballooncolor border=1 width=300px align=center><tr><td class=balloonheadercolor colspan=2 align=center>$smallname</td></tr><tr valign=top><td class=ballooncolor align=center><img border=0 height=200 width=120 src=".MDBS::getImage($row)."></td><td width=80%  class=ballooncolor><div align=left><b>" . Txt("UPLOADED") . ": </b>" . date("m-d-Y", utc_to_tz_time($row["added"])) . "<br /><b>" . Txt("SIZE") . ": </b>". mksize($row["size"]) . "<br /><b>" . Txt("COMPLETED") . ": </b>" . $row["times_completed"] . "<br /></div><div align=left><b>Views: </b>" . $row["views"] . "<br /><b>" . Txt("HITS") . ": </b>" . $row["hits"] . "<br /><b>" . Txt("SEEDERS") . ": </b><font color=green>" . $row["seeders"] . "</font><br /><b>" . Txt("LEECHERS") . ": </b><font color=red>" . $row["leechers"] . "</font><br /><b>" . Txt("BY") . ": </b>" . $row['username'] . "</div></td></tr><tr><td class=balloonheadercolor colspan=2 align=center>".Txt("DESCRIPTION")."</td></tr><tr><td  class=ballooncolor colspan=2>" . $row['descr'] . "</td></tr></table>', CENTER, HEIGHT, 200, WIDTH, 300)\"; onMouseout=\"return nd()\">".$dispname."</a>".$type."");

                    $tags = get_tags($id);
                    print($tags."</td>");
                    
                    break;
                case 'dl':
                    print("<td><a href=\"" . URLROOT . "/download/$id\"><i class='fa fa-download' style='font-size:22px;color:green' title='" . Txt("DOWNLOADED") . "'></i></a></td>");
                    break;
                case 'magnet':
                    // Like Mod
                    if (!CONFIG['FORCETHANKS'] && $row['external'] == 'yes') {
                        print("<td><a href=\"magnet:?xt=urn:btih:" . $row["info_hash"] . "&dn=" . rawurlencode($row['name']) . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><i class='fa fa-magnet fa-2x' style='font-size:23px;color:green' title='" . Txt("DOWNLOADED") . "'></i></a></td>");
                    } elseif (CONFIG['FORCETHANKS']) {
                        $like = LikesModel::fetchRow('user', ['liked =', 'type =', 'user ='], [$id, 'torrent', $USER->has('id')]);
                        if ($like) {
                            if ($USER->permission('can_download') && $row['external'] == 'yes') {
                                print("<td><a href=\"magnet:?xt=urn:btih:" . $row["info_hash"] . "&dn=" . rawurlencode($row['name']) . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><i class='fa fa-magnet fa-2x' style='font-size:23px;color:green' title='" . Txt("DOWNLOADED") . "'></i></a></td>");
                            } else {
                                print("<td><h8><span class='badge border border-1 border-danger'>No Dload</span></h8></td>");
                            }
                        } elseif ($USER->has('id') == $row["owner"] && $row['external'] == 'yes') {
                            print("<td><a href=\"magnet:?xt=urn:btih:" . $row["info_hash"] . "&dn=" . rawurlencode($row['name']) . "&tr=udp://tracker.openbittorrent.com&tr=udp://tracker.publicbt.com\"><i class='fa fa-magnet fa-2x' style='font-size:23px;color:green' title='" . Txt("DOWNLOADED") . "'></i></a></td>");
                        } else {
                            print("<td><a href='" . URLROOT . "/like/thanks?id=$id&type=torrent><h8><span class='badge border border-1 border-danger'>" . Txt("THANKS") . "</span></h8></td>");
                        }
                    } else {
                        print("<td><i class='fa fa-home' style='font-size:23px;color:green' title='" . Txt("LOCAL") . "'></i></td>");
                    }
                    break;
                case 'uploader':
                    echo "<td>";
                    if (($row["anon"] == "yes" || $row["privacy"] == "strong") && $USER->has('id') != $row["owner"] && !$USER->permission('edit_torrents')) {
                        echo "Anonymous";
                    } elseif ($row["username"]) {
                        echo "<a href='" . URLROOT . "/profile/$row[owner]'>" . coloredname($row['owner']) . "</a>";
                    } else {
                        echo "Unknown";
                    }

                    echo "</td>";
                    break;
                case 'tube':
                    if ($row["tube"]) {
                        print("<td><a rel=\"prettyPhoto\"  href=" . $row['tube'] . " ><" . htmlspecialchars($row['tube']) . "><i class='fa fa-youtube-play' style='font-size:32px;color:#ff2c2c' title='" . Txt("YOUTUBE") . "'></i></a></td>");
                    } else {
                        print("<td>-</td>");
                    }
                    break;
                case 'tmdb':
                    if ($row["imdb"]) {
                        print("<td><a href=" . $row['imdb'] . "><img src='" . URLROOT . "/assets/images/misc/imdb.webp'  border='0' width='28' height='25' alt=\"\" /></a></td>");
                    } elseif ($row["tmdb"]) {
                        print("<td><a href=" . $row['tmdb'] . "><img src='" . URLROOT . "/assets/images/misc/tmdb1.webp'  border='0' width='28' height='25' alt=\"\" /></a></td>");
                    } else {
                        print("<td>-</td>");
                    }
                    break;
                case 'comments':
                    print("<td><a href='" . URLROOT . "/comment/$id/torrent'><b>$row[comments]</b></a></td>\n");
                    break;
                case 'nfo':
                    if ($row["nfo"] == "yes") {
                        print("<td><i class='fa fa-file-text-o' title='" . Txt("NFO") . "'></i></a></td>");
                    } else {
                        print("<td>-</td>");
                    }
                    break;
                case 'size':
                    print("<td style='min-width:110px;'>" . mksize($row["size"]) . "</td>");
                    break;
                case 'completed':
                    print("<td><font color='orange'><b>" . number_format($row["times_completed"]) . "</b></font></td>");
                    break;
                case 'seeders':
                    print("<td><font color='green'><b>" . number_format($row["seeders"]) . "</b></font></td>\n");
                    break;
                case 'leechers':
                    print("<td><font color='#ff0000'><b>" . number_format($row["leechers"]) . "</b></font></td>\n");
                    break;
                case 'health':
                    print("<td><img src='" . URLROOT . "/assets/images/health/health_" . health($row["leechers"], $row["seeders"]) . ".webp' alt='' /></td>\n");
                    break;
                case 'external':
                    if (CONFIG['ALLOWEXTERNAL']) {
                        if ($row["external"] == 'yes') {
                            print("<td>" . Txt("E") . "</td>\n");
                        } else {
                            print("<td>" . Txt("L") . "</td>\n");
                        }
                    }
                    break;
                case 'added':
                    print("<td style='min-width:110px;'>" . get_time_elapsed($row['added']) . "</td>");
                    break;
                case 'speed':
                    if ($row["external"] != "yes" && $row["leechers"] >= 1) {
                        $speedQ = PeersModel::fetchColumn("(SUM(downloaded)) / (UNIX_TIMESTAMP('" . get_date_time() . "') - UNIX_TIMESTAMP(started)) AS totalspeed",
                        ['seeder =', 'torrent ='], ['no',$id], "ORDER BY started ASC");
                        $totalspeed = mksize($speedQ) . "/s";
                    } else {
                        $totalspeed = "--";
                    }
                    print("<td>$totalspeed</td>");
                    break;
                case 'wait':
                    if ($wait) {
                        $elapsed = floor((gmtime() - strtotime($row["added"])) / 3600);
                        if ($elapsed < $wait && $row["external"] != "yes") {
                            $color = dechex(floor(127 * ($wait - $elapsed) / 48 + 128) * 65536);
                            print("<td><a href=\"/faq#section46\"><font color=\"$color\">" . number_format($wait - $elapsed) . " h</font></a></td>\n");
                        } else {
                            print("<td>--</td>\n");
                        }

                    }
                    break;
                case 'rating':
                    if (!$row["rating"]) {
                        $rating = "--";
                    } else {
                        $average = ceil($row["rating"]);
                        $rating = "<img src=\"" . URLROOT . "/assets/images/rating/$average.webp\" border=\"0\" alt=\"rating: $average/5\" title=\"rating: $average/5\" /></a>";
                    }
                    //$rating = $row["rating"] . "/5)";
                    print("<td>$rating</td>");
                    break;
                case 'bookmark':
                    print("<td><input type='checkbox' name='ids[]' value='". $row['bookmarkid'] ."' /></td>");
                    break;
                case 'admin':
                    if ($USER->has('class') == 7) {
                        print("<td><input type='checkbox' name='ids[]' value='".$row["id"]."' /></td>");
                    }
                    break;
            }
            
        }

        // Wait Time Check
        if ($wait && !in_array("wait", $cols)) {
            $elapsed = floor((gmtime() - strtotime($row["added"])) / 3600);
            if ($elapsed < $wait && $row["external"] != "yes") {
                $color = dechex(floor(127 * ($wait - $elapsed) / 48 + 128) * 65536);
                print("<td><a href=\"/faq\"><font color=\"$color\">" . number_format($wait - $elapsed) . " h</font></a></td>\n");
            } else {
                print("<td>--</td>\n");
            }
            $colspan++;
        }

        print("</tr>\n");
    }

    print("</table></div><br />\n");
}

   
function topcountry($data)
{
$html = "<div class='table-responsive'><table class='table table-striped'>";
$html .= "<thead><tr>";
$html .= "<th>Rank</th>";
$html .= "<th>Country&nbsp;Flag</th>";
$html .= " <th>Country&nbsp;Name</th>";
$html .= "</tr></thead>";

$num = 0;
foreach ($data as $a) {
    if ($a['flagpic']) {
        $flag = "<img align=center src=".Url("assets/images/languages/$a[flagpic]").">";
    } else {
        $flag = "<img align=center src=".Url('assets/images/languages/unknown.webp').">";
    }
    if ($a['name']) {
        $name = "<b>$a[name]</b>";
    } else {
        $name = "<b>Land of Homeless!</b>";
    }
    $html .= "<tbody><tr>";
    $html .= "<td align=center class=table_col1>$num</td>";
    $html .= "<td align=center class=table_col2>$flag</td>";
    $html .= "<td class=table_col1>$name</td>";
    $html .= "</tr></tbody>";
}
$html .= "</table></div>";
return $html;
}
    
function toptorrent($data)
{
         $html = "<div class='table-responsive'><table class='table table-striped'>";
$html .= "<thead><tr>";
$html .= "<th>Rank</th>";
$html .= "<th>Name</th>";
$html .= "<th><i class='fa fa-check tticon' title='Completed'></i></th>";
$html .= "<th>Traffic</th>";
$html .= "<th><i class='fa fa-upload tticon' title='". Txt("SEEDING") ."'></th>";
$html .= "<th><i class='fa fa-downloadload tticon' title='". Txt("LEECHERS") ."'></i></th>";
$html .= "<th>Total</th>";
$html .= "<th>Ratio</th>";
$html .= "</tr></thead>";

$num = 0;
foreach ($data as $a) {
    ++$num;
    $userratio = $a["leechers"] > 0 ? number_format($a["seeders"] / $a["leechers"], 1) : "---";
    $html .= "<tbody><tr>";
        $html .= "<td>$num</td>";
        $html .= "<td><a href=".Url("torrent/$a[id]/hit")."><b>" . $a["name"] . "</b></a></td>";
       $html .= " <td><font color=#0080FF><b>" . number_format($a["times_completed"]) . "</b></font></td>";
        $html .= "<td>" . mksize($a["data"]) . "</td>";
        $html .= "<td><font color=limegreen><b>" . number_format($a["seeders"]) . "</b></font></td>";
       $html .= " <td><font color=red><b>" . number_format($a["leechers"]) . "</b></font></td>";
        $html .= "<td>" . ($a["leechers"] + $a["seeders"]) . "</td>";
        $html .= "<td>".get_ratio_color($userratio)."</td>";
         $html .= "</tr><tbody>";
}
$html .= "</table></div>";
return $html;
}
    
function topuser($data)
{
         $html = "<div class='table-responsive'><table class='table table-striped'>";
$html .= "<thead><tr>";
$html .= "<th>Rank</td>";
$html .= "<th>User</td>";
$html .= "<th>Uploaded</td>";
$html .= " <th>UL speed</td>";
$html .= "<th>Downloaded</td>";
$html .= "<th>DL speed</td>";
$html .= "<th>Ratio</td>";
$html .= "<th>Joined</td>";
$html .= "</tr></thead>";
$num = 0;
foreach ($data as $a) {
    ++$num;
    $userratio = $a["downloaded"] > 0 ? number_format($a["uploaded"] / $a["downloaded"], 1) : "---";
    
    $html .= "<tbody><tr>";
        $html .= "<td>$num</td>";
        $html .= "<td><a href=".Url("profile/$a[userid]")."><b>" . coloredname($a["userid"]) . "</b>" . "</td>";
        $html .= "<td><font color=limegreen>" . mksize($a["uploaded"]) . "</font></td>";
        $html .= "<td><font color=limegreen>" . mksize($a["upspeed"]) . "/s" . "</font></td>";
        $html .= "<td><font color=#FF2400>" . mksize($a["downloaded"]) . "</font></td>";
        $html .= "<td><font color=#FF2400>" . mksize($a["downspeed"]) . "/s" . "</font></td>";
        $html .= "<td>" . get_ratio_color($userratio) . "</td>";
        $html .= "<td>" . gmdate("d-M-Y", strtotime($a["added"])) . "</td>";
       $html .= " </tr><tbody>";
}
$html .= "</table></div>";
return $html;
}