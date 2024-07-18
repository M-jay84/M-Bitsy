<?php

use App\Libs\User;
use App\Models\BlocksModel;
use App\Models\GuestsModel;

// Translate String To Language
function Txt($s)
{
    global $LANG;
    if ($ret = (isset($LANG[$s]) ? $LANG[$s] : null)) {
        return $ret;
    }
    if ($ret = (isset($LANG["{$s}[0]"]) ? $LANG["{$s}[0]"] : null)) {
        return $ret;
    }
    return $s;
}

// Redidect Page
function Redirect($url, $message1 = false, $time = 5)
{
    $USER = new User(0,0);
    
    $data = [
        'V_title' => 'Message',
        'V_msg' => $message1,
        'V_url' => $url,
        'V_time' => $time,
        'USER' => $USER
    ];
    if (!$data['V_msg']) {
        if (!headers_sent()) {
            header("Location: " . $data['V_url'], true, 302);
            exit();
        } else {
            echo '<script type="text/javascript">';
            echo 'window.location.href="' . $data['V_url'] . '";';
            echo '</script>';
            echo '<noscript>';
            echo '<meta http-equiv="refresh" content="0; url=' . $data['V_url'] . '" />';
            echo '</noscript>';
            exit();
        }
    } else {
    
        // Load View
        template('error_page', $data, 'staffcp');
        //require APPROOT.'/Views/staffcp/error_page.php';
        die();
    }
}

// Get Url
function Url($url = URLROOT, $referer = false)
{
    if ($referer) {
        if (!($_SERVER['HTTP_REFERER'])) {
            return $_SERVER['HTTP_REFERER'];
        } else {
            $path = URLROOT . '/' . $url;
            return $path;
        }
    } elseif ($url == URLROOT) {
        return URLROOT;
    } else {
        $path = URLROOT . '/' . $url;
        return $path;
    }
}

// Begin Page/Block Frame
function block_begin($caption = "-", $p = 3)
{
    ?>
    <div class="tt_block rounded">
        <div class="tt_blockhead text-center">
            <?= $caption ?>
        </div>
        <div class="p-<?= $p ?>">
    <?php
}

// End Page/Block Frame
function block_end()
{
    ?>
        </div>
    </div>
    <?php
}

// Page Column Blocks
function column($align = 'middle', $USER = 0)
{
    $namepace = "\\App\\Libs\\Cache";
    $TTCache = new $namepace();
    if (($blocks = $TTCache->get("block/blocks_$align", 900)) === false) {
        $res = BlocksModel::fetchAll('*', ['position =', 'enabled ='], [$align,1], 'ORDER BY sort');
        $blocks = array();
        foreach ($res as $result) {
            $blocks[] = $result["name"];
        }
        $TTCache->Set("block/blocks_$align", $blocks, 900);
    }
    foreach ($blocks as $blockfilename) {
        $url = $_GET['url'] ?? '';
        if (!in_array($url, ISURL) && $align != 'middle') {
            include "../App/Views/block/" . $blockfilename . "_block.php";
        }
    }
}

// View Template
function template($file, $data = [], $page = false)
{
    // Debug
    //echo '<b>$Data debug vars passed to template from the controller<br><pre>'.print_r($data, true).'</pre><br>';
    $session_id = isset($_SESSION['id']) ? $_SESSION['id'] : 0;
    if (!$session_id) {
        GuestsModel::add();
    }

    if ($page == 'user' && file_exists("../App/Views/user/$file.php")) {
        require_once "assets/themes/" . (isset($_SESSION['stylesheet']) ? $_SESSION['stylesheet'] : CONFIG['DEFAULTTHEME']) . "/header.php";
        extract($data);
        require_once "../App/Views/$page/$file.php";
        require_once "assets/themes/" . (isset($_SESSION['stylesheet']) ? $_SESSION['stylesheet'] : CONFIG['DEFAULTTHEME']) . "/footer.php";
    } elseif ($page == 'admin' && file_exists("../App/Views/admin/$file.php")) {
        require_once APPROOT . "/Views/staffcp/admin_header.php";
        extract($data);
        require_once "../App/Views/$page/$file.php";
        require_once APPROOT . "/Views/staffcp/admin_footer.php";
    } elseif (file_exists("../App/Views/$page/$file.php")) {
        require_once "../App/Views/$page/" . $file . ".php";
        return;
    } else {
        Redirect(Url(), "View $file does not exist");
    }
    return;
}