<?php

// Function that calculates the Hours Minutes Seconds of a Timestamp 
function mkprettytime($s)
{
    if ($s < 0) {
        $s = 0;
    }

    $t = array();
    $t["day"] = floor($s / 86400);
    $s -= $t["day"] * 86400;
    $t["hour"] = floor($s / 3600);
    $s -= $t["hour"] * 3600;
    $t["min"] = floor($s / 60);
    $s -= $t["min"] * 60;
    $t["sec"] = $s;

    if ($t["day"]) {
        return $t["day"] . "d " . sprintf("%02d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
    }
    if ($t["hour"]) {
        return sprintf("%d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
    }
    return sprintf("%d:%02d", $t["min"], $t["sec"]);
}

// Get Time As INT According To TimeZone
function gmtime()
{
    return sql_timestamp_to_unix_timestamp(get_date_time());
}

// Convert Timestamp To INT
function sql_timestamp_to_unix_timestamp($s)
{
    return mktime(substr($s, 11, 2), substr($s, 14, 2), substr($s, 17, 2), substr($s, 5, 2), substr($s, 8, 2), substr($s, 0, 4));
}

// Obtain Week Day Hour Minute Second From Int
function get_elapsed_time($ts)
{
    $mins = floor((gmtime() - $ts) / 60);
    $hours = floor($mins / 60);
    $mins -= $hours * 60;
    $days = floor($hours / 24);
    $hours -= $days * 24;
    $weeks = floor($days / 7);
    $days -= $weeks * 7;
    $t = "";
    if ($weeks > 0) {
        return "$weeks wk" . ($weeks > 1 ? "s" : "");
    }
    if ($days > 0) {
        return "$days day" . ($days > 1 ? "s" : "");
    }
    if ($hours > 0) {
        return "$hours hr" . ($hours > 1 ? "s" : "");
    }
    if ($mins > 0) {
        return "$mins min" . ($mins > 1 ? "s" : "");
    }
    return "< 1 min";
}

// Get Timestamp - Option Pass Format To Data
function get_date_time($timestamp = 0)
{
    if ($timestamp) {
        return date("Y-m-d H:i:s", $timestamp);
    } else {
        return gmdate("Y-m-d H:i:s");
    }
}

// Get Timestamp From User Time Zone
function utc_to_tz($timestamp = 0)
{
    global $tzs;
    if (method_exists("DateTime", "setTimezone")) {
        if (!$timestamp) {
            $timestamp = get_date_time();
        }
        $date = new DateTime($timestamp, new DateTimeZone("UTC"));
        $ZONE = $tzs[$_SESSION['tzoffset']][1] ?? "Europe/London";
        $date->setTimezone(new DateTimeZone($ZONE));
        return $date->format('Y-m-d H:i:s');
    }
    if (!is_numeric($timestamp)) {
        $timestamp = sql_timestamp_to_unix_timestamp($timestamp);
    }
    if ($timestamp == 0) {
        $timestamp = gmtime();
    }
    $timestamp = $timestamp + ($_SESSION['tzoffset'] * 60);
    if (date("I")) {
        $timestamp += 3600;
    }
    // DST Fix
    return date("Y-m-d H:i:s", $timestamp);
}

// Get Int From User Time Zone
function utc_to_tz_time($timestamp = 0)
{
    global $tzs;
    if (method_exists("DateTime", "setTimezone")) {
        if (!$timestamp) {
            $timestamp = get_date_time();
        }
        $date = new \DateTime($timestamp, new \DateTimeZone("UTC"));
        $ZONE = $tzs[$_SESSION['tzoffset']][1] ?? "Europe/London";
        $date->setTimezone(new \DateTimeZone($ZONE));
        return sql_timestamp_to_unix_timestamp($date->format('Y-m-d H:i:s'));
    }
    if (!is_numeric($timestamp)) {
        $timestamp = sql_timestamp_to_unix_timestamp($timestamp);
    }
    if ($timestamp == 0) {
        $timestamp = gmtime();
    }
    $timestamp = $timestamp + ($_SESSION['tzoffset'] * 60);
    if (date("I")) {
        $timestamp += 3600;
    }
    // DST Fix
    return $timestamp;
}

// Get Interval Between 2 Dates As Int
function DateDiff($start, $end)
{
    if (!is_numeric($start)) {
        $start = sql_timestamp_to_unix_timestamp($start);
    }
    if (!is_numeric($end)) {
        $end = sql_timestamp_to_unix_timestamp($end);
    }
    return ($end - $start);
}

// Obtain Week Day Hour Minute Second From Timestamp
function get_time_elapsed($datetime, $full = false)
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;
    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }
    if (!$full) {
        $string = array_slice($string, 0, 1);
    }

    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

// Drop Down for Time Zones
function timeZoneDropDown($tzoffset)
{
    global $tzs;
    $tz = '';
    ksort($tzs);
    reset($tzs);
    foreach ($tzs as $key => $val) {
        if ($tzoffset == $key) {
            $tz .= "<option value=\"$key\" selected='selected'>$val[0]</option>\n";
        } else {
            $tz .= "<option value=\"$key\">$val[0]</option>\n";
        }
    }
    return $tz;
}

// $type=date/string || $target=sqlresult || $amount=add/sub date
function modify($type, $target, $amount)
{
    if ($type == 'date') {
        $date = date_create($target);
        date_modify($date, $amount);
    } elseif ($type == 'time') {
        $date = date_create();
        date_timestamp_set($date, $target);
        date_modify($date, $amount);
    }

    return date_format($date, 'Y-m-d H:i:s');
}

// Time Zones Array
// Php DateTimeZone (Language Default Used is English !!!)
$tzs = array();
$tzs["-720"] = array("(GMT - 12:00 hours) Enitwetok, Kwajalien", "Pacific/Kwajalein");
$tzs["-660"] = array("(GMT - 11:00 hours) Midway Island, Samoa", "Pacific/Samoa");
$tzs["-600"] = array("(GMT - 10:00 hours) Hawaii", "Etc/GMT+10");
$tzs["-540"] = array("(GMT - 9:00 hours) Alaska", "America/Anchorage");
$tzs["-480"] = array("(GMT - 8:00 hours) Pacific Time (US &amp; Canada)", "America/Los_Angeles");
$tzs["-420"] = array("(GMT - 7:00 hours) Mountain Time (US &amp; Canada)", "America/Denver");
$tzs["-360"] = array("(GMT - 6:00 hours) Central Time (US &amp; Canada), Mexico City", "America/Chicago");
$tzs["-300"] = array("(GMT - 5:00 hours) Eastern Time (US &amp; Canada), Bogota, Lima, Quito", "America/New_York");
$tzs["-240"] = array("(GMT - 4:00 hours) Atlantic Time (Canada), Caracas, La Paz", "America/Guadeloupe");
$tzs["-210"] = array("(GMT - 3:30 hours) Newfoundland", "America/St_Johns");
$tzs["-180"] = array("(GMT - 3:00 hours) Brazil, Buenos Aires, Georgetown, Falkland Isl.", "Etc/GMT+3");
$tzs["-120"] = array("(GMT - 2:00 hours) Mid-Atlantic, Ascention Isl., St Helena", "Atlantic/St_Helena");
$tzs["-60"] = array("(GMT - 1:00 hour) Azores, Cape Verde Islands", "Atlantic/Azores");
$tzs["0"] = array("(GMT) Casablanca, Dublin, Edinburgh, London, Lisbon, Monrovia", "Europe/London");
$tzs["60"] = array("(GMT + 1:00 hour) Amsterdam, Berlin, Copenhagen, Madrid, Paris, Rome", "Europe/Rome");
$tzs["120"] = array("(GMT + 2:00 hours) Kaliningrad, South Africa, Warsaw", "Europe/Kaliningrad");
$tzs["180"] = array("(GMT + 3:00 hours) Baghdad, Riyadh, Moscow, Nairobi", "Africa/Nairobi");
$tzs["210"] = array("(GMT + 3:30 hours) Tehran", "Asia/Tehran");
$tzs["240"] = array("(GMT + 4:00 hours) Adu Dhabi, Baku, Muscat, Tbilisi", "Asia/Tbilisi");
$tzs["270"] = array("(GMT + 4:30 hours) Kabul", "Asia/Kabul");
$tzs["300"] = array("(GMT + 5:00 hours) Ekaterinburg, Islamabad, Karachi, Tashkent", "Asia/Tashkent");
$tzs["330"] = array("(GMT + 5:30 hours) Bombay, Calcutta, Madras, New Delhi", "Asia/Calcutta");
$tzs["360"] = array("(GMT + 6:00 hours) Almaty, Dhaka, Khulna", "Asia/Dhaka");
$tzs["420"] = array("(GMT + 7:00 hours) Bangkok, Hanoi, Jakarta", "Asia/Bangkok");
$tzs["480"] = array("(GMT + 8:00 hours) Beijing, Hong Kong, Perth, Singapore, Taipei", "Asia/Taipei");
$tzs["540"] = array("(GMT + 9:00 hours) Osaka, Sapporo, Seoul, Tokyo, Yakutsk", "Asia/Tokyo");
$tzs["570"] = array("(GMT + 9:30 hours) Adelaide, Darwin", "Australia/Adelaide");
$tzs["600"] = array("(GMT + 10:00 hours) Melbourne, Papua New Guinea, Sydney, Vladivostok", "Australia/Sydney");
$tzs["660"] = array("(GMT + 11:00 hours) Magadan, New Caledonia, Solomon Islands", "Asia/Magadan");
$tzs["720"] = array("(GMT + 12:00 hours) Auckland, Wellington, Fiji, Marshall Island", "Pacific/Auckland");