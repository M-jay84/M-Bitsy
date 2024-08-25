<?php
if ($_SESSION['id']) {
    block_begin("Qbit");
    print("<center><a href='https://www.qbittorrent.org/download.php'><font size='4' color='#c17b11'><b>Download</b></font></a></center>");
    print("<center><a href='https://www.qbittorrent.org/download.php'><img src='".Url('assets/images/misc/qbittorrent.webp')."'  width='80%' height='80' alt='' /></a></center>");
    block_end();
}