<?php

namespace App\Libs;

/*
* Check Ip & Connection
*/
class Ip
{
    // Check IP for ban and Redirect
    public static function checkIpBan($ip)
    {
        if (self::isIpV6($ip)) {
            $nip = self::ipToLong6($ip);
	    } else {
            $nip = ip2long($ip);
        }
        $res = DB::fetchAll('bans', '*', ['type ='], ['ip']);
        foreach ($res as $row) {
            $banned = false;
            if (self::isIpV6($row["first"]) && self::isIpV6($row["last"]) && self::isIpV6($ip)) {
                $row["first"] = self::ipToLong6($row["first"]);
                $row["last"] = self::ipToLong6($row["last"]);
                $banned = bccomp($row["first"], $nip) != -1 && bccomp($row["last"], $nip) != -1;
            } else {
                $row["first"] = ip2long($row["first"]);
                $row["last"] = ip2long($row["last"]);
                $banned = $nip >= $row["first"] && $nip <= $row["last"];
            }
            if ($banned) {
                header("HTTP/1.0 403 Forbidden");
                echo '<html><head><title>Forbidden</title> </head><body> <h1>Forbidden</h1>Unauthorized IP address.<br> </body></html>';
                die;
            }
        }
    }

    // IP Check
    public static function checkIP()
    {
        $ip = Ip::getIP();
        $ipq = DB::fetchColumn('users', 'count(id)', ['ip ='], [$ip]);
        if ($ipq >= CONFIG['ACCOUNTMAX']) {
            showMessage('login', LANG["IP_USED"]);
        }
    }

    // IP Validation Function
    public static function validIP($ip)
    {
        if (strtolower($ip) === "unknown") {
            return false;
        }
        // generate ipv4 network address
        $ip = ip2long($ip);
        // if the ip is set and not equivalent to 255.255.255.255
        if ($ip !== false && $ip !== -1) {
            // make sure to get unsigned long representation of ip due to discrepancies
            // between 32 and 64 bit OSes and signed numbers (ints default to signed in PHP)
            $ip = sprintf("%u", $ip);
            // do private network range checking
            if ($ip >= 0 && $ip <= 50331647) {
                return false;
            }
            if ($ip >= 167772160 && $ip <= 184549375) {
                return false;
            }
            if ($ip >= 2130706432 && $ip <= 2147483647) {
                return false;
            }
            if ($ip >= 2851995648 && $ip <= 2852061183) {
                return false;
            }
            if ($ip >= 2886729728 && $ip <= 2887778303) {
                return false;
            }
            if ($ip >= 3221225984 && $ip <= 3221226239) {
                return false;
            }
            if ($ip >= 3232235520 && $ip <= 3232301055) {
                return false;
            }
            if ($ip >= 4294967040) {
                return false;
            }
        }
        return true;
    }

    // Get Ip
    public static function getIP()
    {
        // Cloudflare
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
            return $ip;
        }
        
        // check for shared internet/ISP IP
        if (!empty($_SERVER['HTTP_CLIENT_IP']) && self::validIP($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        // check for IPs passing through proxies
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // check if multiple ips exist in var
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
                $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                foreach ($iplist as $ip) {
                    if (self::validIP($ip)) {
                        return $ip;
                    }
                }
            } else {
                if (self::validIP($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    return $_SERVER['HTTP_X_FORWARDED_FOR'];
                }
            }
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED']) && self::validIP($_SERVER['HTTP_X_FORWARDED'])) {
            return $_SERVER['HTTP_X_FORWARDED'];
        }
        if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && self::validIP($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && self::validIP($_SERVER['HTTP_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        }
        if (!empty($_SERVER['HTTP_FORWARDED']) && self::validIP($_SERVER['HTTP_FORWARDED'])) {
            return $_SERVER['HTTP_FORWARDED'];
        }
        // return unreliable ip since all else failed
        return $_SERVER['REMOTE_ADDR'];
    }

    // Function For Verification If IP Address IPV6 Format
    public static function isIpV6($s)
    {
        return is_int(strpos($s, ":"));
    }

    // Taken from php.net comments
    public static function ipToLong6($ipv6)
    {
        $ipv6long = '';
        $ip_n = inet_pton($ipv6);
        $bits = 15; // 16 x 8 bit = 128bit
        while ($bits >= 0) {
            $bin = sprintf("%08b", (ord($ip_n[$bits])));
            $ipv6long = $bin . $ipv6long;
            $bits--;
        }
        // Enable gmp extension
        if (extension_loaded("gmp")) {
            return gmp_strval(gmp_init($ipv6long, 2), 10);
        }
    }

}