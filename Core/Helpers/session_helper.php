<?php

// Goggle v2 Captcha
function captcha_response($captcha)
{
    if (CONFIG['CAPTCHA_ON']) {
        if (!$captcha) {
            showMessage(Url('login'), LANG['CAPTCHA']);
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
            showMessage(Url('logout'), LANG["CSRF_FAILED"]);
            return false;
        } else {
            setcookie("csrf_token", "", time()-45);
            return true;
        }
    } else {
        return true;
    }
}