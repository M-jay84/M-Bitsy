<?php

namespace App\Libs;

/*
* Validate Data
*/
class Validate
{
    // Multi $_POST ids
    public static function multiIds($value)
    {
        $ids = $_POST[$value];
        if (!$ids) {
            showMessage(URLROOT, LANG["MISSING_FORM_DATA"]);
        }
        $int_array = array_map('intval', $ids);
        $string_of_ids = implode(', ', $int_array);

        return $string_of_ids;
    }

    // Check $_POST Input
    public static function Input($value, $type = null)
    {
        if ($type == 'email') {
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                showMessage(URLROOT, LANG["INVALID_EMAIL_ADDRESS"]);
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
    public static function captcha_response($captcha)
    {
        if (CONFIG['CAPTCHA_ON']) {
            if (!$captcha) {
                showMessage('login', LANG['CAPTCHA']);
            }
            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . CONFIG['CAPTCHA_SECRET'] . '&response=' . $captcha);
            $responseData = json_decode($verifyResponse);
            if ($responseData->success) {
                // its successfull so let it go
            }
        }
    }
}
