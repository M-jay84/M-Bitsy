<?php
use App\Libs\Ip;

if (!$USER->has('id') && !CONFIG['MEMBERSONLY'] || $USER->has('id')) {
    block_begin(Txt("CONNECTION"));
    $osName = Ip::operatingSystem();
    $osVersion = Ip::osVersion();
    $browserName = Ip::browser()['browser'];
    $browserVersion = Ip::browserVersion();
    $ip = Ip::getip();
    echo '<font color=orange><b>Op Sys&nbsp;</b></font>' . $osName;
    echo '<br><font color=orange><b>Version&nbsp;</b></font>' . $osVersion;
    echo '<br><font color=orange><b>Browser&nbsp;</b></font>' . $browserName;
    echo '<br><font color=orange><b>Version&nbsp;</b></font>' . $browserVersion;
    echo '<br><font color=orange><b>Ip&nbsp;</b></font>' . $ip; ?><?php
    block_end();
}