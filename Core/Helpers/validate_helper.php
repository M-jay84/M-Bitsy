<?php

// Multi $_POST ids
function multiIds($value)
{
    $ids = $_POST[$value];
    if (!$ids) {
        Redirect(Url(), Txt("MISSING_FORM_DATA"));
    }
    $int_array = array_map('intval', $ids);
    $string_of_ids = implode(', ', $int_array);

    return $string_of_ids;
}

// Check $_POST Input
function Input($value, $type = null)
{
    if ($type == 'email') {
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            Redirect(Url(), Txt("INVALID_EMAIL_ADDRESS"));
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

// Goggle v2 Captcha
function captcha_response($captcha)
{
    if (CONFIG['CAPTCHA_ON']) {
        if (!$captcha) {
            Redirect(Url('login'), Txt('CAPTCHA'));
        }
        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . CONFIG['CAPTCHA_SECRET'] . '&response=' . $captcha);
        $responseData = json_decode($verifyResponse);
        if ($responseData->success) {
            // its successfull so let it go
        }
    }
}

// Display Captcha
function captcha_html()
{
    if (CONFIG['CAPTCHA_ON']) {
        $captcha_form = "<center><div class='g-recaptcha' data-theme='light' data-sitekey='" . CONFIG['CAPTCHA_KEY'] . "'></div><center>";
        return $captcha_form;
    }
}

// Destroy All Cookies
function destroy_cookies()
{
    setcookie('id', '', time()-45);
    setcookie('password', '', time()-45);
    setcookie('PHPSESSID', '', time()-45);
    $_SESSION = array();
    unset($_SESSION);
    @session_destroy();
}

// Set All Cookie Values
function set_cookies($id, $pass)
{
    setcookie('id', $id, time()+60*60*24*30 );
    setcookie('password', $pass, time()+60*60*24*30 );
}

// Create CSRF Cookie
function csrf_token()
{
    // Check if a token is present for the current session
    if (!isset($_COOKIE['csrf_token'])) {
        $token = base64_encode(openssl_random_pseudo_bytes(32));
        setcookie("csrf_token", $token, time()+60*60*24*30);
    } else {
        $token = $_COOKIE['csrf_token'];
    }
    return $token;
}

// Create CSRF Cookie
function csrf_check()
{
    if (CONFIG['CSRF_Token']) {
        if (!$_POST["csrf_token"] == $_COOKIE["csrf_token"]) {
            // Reset token
            setcookie("csrf_token", "", time()-45);
            Redirect(Url('logout'), Txt("CSRF_FAILED"));
            return false;
        } else {
            setcookie("csrf_token", "", time()-45);
            return true;
        }
    } else {
        return true;
    }
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

// Privacy Level
function priv($name, $descr)
{
    if ($_SESSION['privacy'] == $name) {
        return "<input type=\"radio\" name=\"privacy\" value=\"$name\" checked=\"checked\" /> $descr";
    }
    return "<input type=\"radio\" name=\"privacy\" value=\"$name\" /> $descr";
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