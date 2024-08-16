<?php

// Function That Allows HTML Encoding
function encodehtml($s, $linebreaks = true)
{
    $s = str_replace("<", "&lt;", str_replace("&", "&amp;", $s));
    if ($linebreaks) {
        $s = nl2br($s);
    }
    return $s;
}

// URL To Link
function format_urls($s)
{
    return preg_replace(
        "/(\A|[^=\]'\"a-zA-Z0-9])((http|ftp|https|ftps|irc):\/\/[^<>\s]+)/i",
        "\\1<a href='\\2'>\\2</a>", $s);
}

// Parse String (BBcode)
function format_comment($text)
{
    $s = $text;
    $s = htmlspecialchars($s);
    $s = format_urls($s);
    // [*] [list]
    $s = preg_replace("/\[\*\]/", "<li>", $s);
    $s = preg_replace("#\[list\](.+)\[/list\]#isU", "<li style= 'margin-left: 20px;'>$1</li>", $s);
    // [center]
    $s = preg_replace("#\[center\](.+)\[/center\]#isU", "<center>$1</center>", $s);
    // [b]Bold[/b]
    $s = preg_replace("/\[b\]((\s|.)+?)\[\/b\]/", "<b>\\1</b>", $s);
    // [i]Italic[/i]
    $s = preg_replace("/\[i\]((\s|.)+?)\[\/i\]/", "<i>\\1</i>", $s);
    // [u]Underline[/u]
    $s = preg_replace("/\[u\]((\s|.)+?)\[\/u\]/", "<u>\\1</u>", $s);
    // [s]Line[/s]
    $s = preg_replace("#\[s\](.+)\[/s\]#isU", "<span style='text-decoration:line-through;'>$1</span>", $s);
    // [url]Links[/url]
    $s = preg_replace("#\[url\]((?:ftp|https?)://.*?)\[/url\]#i", "<a href='$1'>$1</a>", $s);
    $s = preg_replace("#\[url=((?:ftp|https?)://.*?)\](.*?)\[/url\]#i", "<a href='$1'>$2</a>", $s);
    // [img]Image[/img]
    $s = preg_replace("#\[img\]((?:ftp|https?)://[a-z0-9._/-]+(\.gif|\.jpg|\.png|\.bmp|\.jpeg))\[/img\]#isU", "<img src='$1' class='scaleA'/>", $s);
    $s = preg_replace("#\[img=((?:ftp|https?)://[a-z0-9._/-]+(\.gif|\.jpg|\.png|\.bmp|\.jpeg))\]\[/img\]#isU", "<img src='$1' class='scaleA'/>", $s);
    // [Code]Code[/Code]
    $s = preg_replace("#\[code\](.+)\[/code\]#isU", "<b> Code : </b><pre><code><div class='scaleA' style='max-height:400px;white-space: nowrap;'  readonly='readonly'>$1</div></code></pre>", $s);
    // [Quote]Quote[/Quote]
    while (preg_match("#\[quote\](.+)\[/quote\]#isU", $s)) {
        $s = preg_replace("#\[quote\](.+)\[/quote\]#isU", "<blockquote><strong> Quote : </strong></blockquote><div class='scaleA' style='padding:5px; border: 1px black dotted'>$1</div><br />", $s);
    }
    while (preg_match("#\[quote=(.+)\](.+)\[/quote\]#isU", $s)) {
        $s = preg_replace("#\[quote=(.+)\](.+)\[/quote\]#isU", "<blockquote><strong>$1 Quote : </strong></blockquote><div class='scaleA' style='padding:5px; border: 1px black dotted'>$2</div><br />", $s);
    }
    // [video]
    $s = preg_replace("#\[video\](https://www.youtube.com.*v=.+)\[\/video\]#isU", "<center><embed src='https://www.youtube.com/v/$1' type='video' height='140'></embed></center>", $s);
    // [audio]
    $s = preg_replace("#\[audio\]((?:ftp|https?)://[a-z0-9._/-]+(\.mp3|\.wav|\.wma|\.aac|\.bwf|\.ogg|\.ac3|\.flac|\.asx|\.pls|\.alac))\[/audio\]#isU", "<img src='$1' />", $s);
    // [color=text]
    $s = preg_replace("#\[color=([a-zA-Z]+)\](.+)\[/color\]#isU", "<font color='$1'>$2</font>", $s);
    // [color=#hexcode]
    $s = preg_replace("#\[color=\#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})\](.+)\[/color\]#isU", "<font color='$1'>$2</font>", $s);
    // Size of Style
    $s = preg_replace("#\[size=([1-7])\](.+)\[/size\]#isU", "<font size='$1'>$2</font>", $s);
    // Type of Style
    $s = preg_replace("#\[font=([a-zA-Z].*?)\](.+)\[/font\]#isU", "<font face='$1'>$2</font>", $s);
    // Blink
    $s = preg_replace("#\[blink\](.+)\[/blink\]#isU", "<div id='blink'>$1</div>", $s);
    // Scroller
    $s = preg_replace("#\[df\](.+)\[/df\]#isU", "<div class='marquee'>$1</div>", $s);
    // [align=(center|left|right)]
    $s = preg_replace("/\[align=([a-zA-Z]+)\](.+?)\[\/align\]/is", "<div style=\"text-align:\\1\">\\2</div>", $s);
    // [hr]
    $s = preg_replace("/\[hr\]/i", "<hr />", $s);
    //[hr=#ffffff] [hr=red]
    $s = preg_replace("/\[hr=((#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])|([a-zA-z]+))\]/i", "<hr color=\"\\1\"/>", $s);
    // Linebreaks
    $s = nl2br($s);
    // Maintain spacing
    $s = str_replace("  ", " &nbsp;", $s);

    return $s;
}

// get image embeded image
function data_uri($file, $mime)
{
    $contents = file_get_contents($file);
    $base64 = base64_encode($contents);
    return ('data:' . $mime . ';base64,' . $base64);
}

// Returns The Size
function mksize($s, $precision = 2)
{
    $suf = array("B", "kB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");

    for ($i = 1, $x = 0; $i <= count($suf); $i++, $x++) {
        if ($s < pow(1024, $i) || $i == count($suf)) // Change 1024 to 1000 if you want 0.98GB instead of 1,0000MB
        {
            return number_format($s / pow(1024, $x), $precision) . " " . $suf[$x];
        }
    }
}

// Shorten Name
function CutName($vTxt, $Car)
{
    if (strlen($vTxt) > $Car) {
        return substr($vTxt, 0, $Car) . "...";
    }
    return $vTxt;
}

// Returns a numeric conversion according to a string
function strtobytes($str)
{
    $str = trim($str);
    if (!preg_match('!^([\d\.]+)\s*(\w\w)?$!', $str, $matches)) {
        return 0;
    }

    $num = $matches[1];
    $suffix = strtolower($matches[2]);
    switch ($suffix) {
        case "tb": // TeraByte
            return $num * 1099511627776;
        case "gb": // GigaByte
            return $num * 1073741824;
        case "mb": // MegaByte
            return $num * 1048576;
        case "kb": // KiloByte
            return $num * 1024;
        case "b": // Byte
        default:
            return $num;
    }
}

// Upload Image - torrent/edit
function uploadimage($x, $imgname, $tid)
{
    $imagesdir = UPLOADDIR . "/images";
    $allowed_types = ALLOWEDIMAGETYPES;
    if (!($_FILES["image$x"]["name"] == "")) {
        if ($imgname != "") {
            $img = "$imagesdir/$imgname";
            unlink($img);
        }
        $y = $x + 1;
        $im = getimagesize($_FILES["image$x"]["tmp_name"]);
        if (!$im[2]) {
            Redirect(URLROOT . $_SERVER["HTTP_REFERER"], "Invalid Image $y.");
        }
        if (!array_key_exists($im['mime'], $allowed_types)) {
            Redirect(URLROOT . $_SERVER["HTTP_REFERER"], Txt("INVALID_FILETYPE_IMAGE"));
        }
        if ($_FILES["image$x"]["size"] > IMAGEMAXFILESIZE) {
            Redirect(URLROOT . $_SERVER["HTTP_REFERER"], sprintf(Txt("INVAILD_FILE_SIZE_IMAGE"), $y));
        }
        $uploaddir = "$imagesdir/";
        $ifilename = $tid . $x . $allowed_types[$im['mime']];
        copyImage($_FILES["image$x"]["tmp_name"], $uploaddir . $ifilename);
        return $ifilename;
    }
}

// Save Image
function copyImage($source, $destination, $redirect = URLROOT)
{
    if (extension_loaded('gd') && USE_WEBP) {
        // compress size max 100
        $compression_quality = 25;
        // set type
        $type = substr($destination, -3);
        $jpeg = substr($destination, -4);
        // set destination
        $new_destination = substr($destination, 0, -3) . 'webp';

        if ($type == 'png') {
            $image = imagecreatefrompng($source);
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
        } elseif ($type == 'jpg') {
            $image = imagecreatefromjpeg($source);
        } elseif ($type == 'gif') {
            $image = imagecreatefromgif($source);
            imagepalettetotruecolor($image);
        } elseif ($jpeg == 'jpeg') {
            $image = imagecreatefromjpeg($source);
            $new_destination = substr($destination, 0, -4) . 'webp';
        } else {
            Redirect($redirect, sprintf(Txt("Strange Image Type - Only Gif, Png, Jpeg & Jpg allowed")));
        }

        // convert image
        $result = imagewebp($image, $new_destination, $compression_quality);
        // check result
        if (false === $result) {
            Redirect($redirect, sprintf(Txt("No Image Result")));
        }
        // destroy temp
        imagedestroy($image);
        return $new_destination;
    } else {
        $copy = copy($source, $destination);
        if (!$copy) {
            Redirect($redirect, Txt("IMAGE_UPLOAD_FAILED"));
        }
        return $destination;
    }
}

// Display Extension Icon
function getexttype($ext = '')
{
    $ext = strtolower($ext);
    $music = array('mp3', 'wav', 'flac', 'm3u');
    $video = array('mp4', 'avi', 'mkv', 'flv', 'wmv');
    $file = array('txt', 'pdf', 'doc', 'zip', 'nfo', 'srt', 'exe');
    $image = array('jpeg', 'gif', 'png');
    if ($ext == false || $ext == '') {
        $filetype_icon = "&nbsp;<i class='fa fa-question'></i>";
    } else if (in_array($ext, $music)) {
        $filetype_icon = "&nbsp;<i class='fa fa-music'></i>";
    } else if (in_array($ext, $video)) {
        $filetype_icon = "&nbsp;<i class='fa fa-film'></i>";
    } else if (in_array($ext, $file)) {
        $filetype_icon = "&nbsp;<i class='fa fa-file'></i>";
    } else if (in_array($ext, $image)) {
        $filetype_icon = "&nbsp;<i class='fa fa-picture-o'></i>";
    }
    return $filetype_icon;
}