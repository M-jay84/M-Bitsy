<?php

// Redidect Page
function showMessage($url, $message1)
{
    $data = [
        'page_title' => 'Message',
        'V_msg' => $message1,
        'V_url' => $url
    ];
    extract($data);
    require_once CONFIG['APPROOT'] . "/Views/user/error_page.php";
    exit();
}

// Get Redirect
function Url($url = CONFIG['URLROOT'], $go_to = false)
{
    if ($go_to) {
        if (!headers_sent()) {
            header("Location: " . $url, true, 302);
            exit();
        } else {
            echo '<script type="text/javascript">';
            echo 'window.location.href="' . $url . '";';
            echo '</script>';
            echo '<noscript>';
            echo '<meta http-equiv="refresh" content="3; url=' . $url . '" />';
            echo '</noscript>';
            exit();
        }
    } elseif ($url == CONFIG['URLROOT']) {
        return CONFIG['URLROOT'];
    } else {
        $path = CONFIG['URLROOT'] . '/' . $url;
        return $path;
    }
}

// Check $_POST Input
function Input($value, $type = null)
{
    if ($type == 'email') {
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            showMessage(CONFIG['URLROOT'], LANG["INVALID_EMAIL_ADDRESS"]);
        }
        return $_POST[$value];
    }
    if ($type == 'int') {
        if (filter_var($_POST[$value], FILTER_VALIDATE_INT)) {
            return $_POST[$value];
        } else {
            return 0;
        }
    }
    if (isset($_POST[$value])) {
        return trim(strip_tags(filter_input(INPUT_POST, $value)));
    }
    return false;
}

// Create Random String
function mksecret($len = 20)
{
    $str = null;
    $chars = array_merge(range(0, 9), range("A", "Z"), range("a", "z"));
    shuffle($chars);
    $x = count($chars) - 1;
    for ($i = 1; $i <= $len; $i++) {
        $str .= $chars[mt_rand(0, $x)];
    }
    return $str;
}

// Check ID 
function Id($id)
{
    return is_numeric($id) && ($id > 0) && (floor($id) == $id);
}

// Check Valid User Name
function username($username)
{
    $allowedchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-";
    for ($i = 0; $i < strlen($username); ++$i)
        if (strpos($allowedchars, $username[$i]) === false)
            return false;
    return true;
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