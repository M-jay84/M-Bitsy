<?php

// Smilies Array
$emoji = array(
    "😊" => "smile",
    "😞" => "sad",
    "😉" => "wink",
    "🤩" => "grinstar",
    "😐" => "plain",
    "😕" => "confused",
    "🤓" => "glasses",
    "😬" => "eek",
    "😎" => "cool",
    "😢" => "crying",
    "😘" => "kiss",
    "😇" => "angel",
    "😈" => "devilish",
    "🤣" => "roflmao",
    "😴" => "sleep",
    "🥰" => "love",
    '💩' => "poop",
    "😲" => "shocked",
    "😡" => "mad",
    "🙄" => "rolleyes",
    "🤢" => "sick",
    "😂" => "crylaugh",
    "🔥" => "fire",
    "🦄" => "unicorn",
    "😺" => "cat",
    "😀" => "grin",
    "👍" => "thumbsup",
    "👎" => "thumbsdown",
    "👌" => 'ok',
    "🙈" => "monkey",
    "🆘" => "help",
    "⚠" => "warn",
    "💣" => "bomb",
    "💡" => "idea",
);

// $form = Form Name
// $name = Name of Text Area (Textarea)
// $content = Content Textarea (Only to edit Pages, etc ...)
 
// Function To Display Input Box With BBCodes And Smilies
function textbbcode($form, $name, $content = "")
{
    // Includen JS Function For BBCode
    require "assets/js/BBTag.js"; ?>

    <div class="row justify-content-md-center">
    <div class="col-12  text-center"> <?php

    // bbcode
    print("<i class='p-1 fa fa-bold' id='BBCode' name='Bold' style='height:20px; width:20px;' onclick=\"bbcomment('[b]', '[/b]')\" title='Bold' /></i>");
    print("<i class='p-1 fa fa-italic' id='BBCode' name='Italic' style='height:20px; width:20px;' onclick=\"bbcomment('[i]', '[/i]')\" title='Italic' /></i>");
    print("<i class='p-1 fa fa-underline' id='BBCode' name='Highlight' style='height:20px; width:20px;' onclick=\"bbcomment('[u]', '[/u]')\" title='Highlight'/></i>");
    print("<i class='p-1 fa fa-strikethrough' id='BBCode' name='Strike' style='height:20px; width:20px;' onclick=\"bbcomment('[s]', '[/s]')\" title='Strike' /></i>");
    print("<i class='p-1 fa fa-list' id='BBCode' name='List' style='height:20px; width:20px;' onclick=\"bbcomment('[list]', '[/list]')\" title='List'	/></i>");
    print("<i class='p-1 fa fa-quote-right' id='BBCode' name='Quote' style='height:20px; width:20px;'	onclick=\"bbcomment('[quote]', '[/quote]')\" title='Quote'	/></i>");
    print("<i class='p-1 fa fa-code' id='BBCode' name='Code' style='height:20px; width:20px;' onclick=\"bbcomment('[code]', '[/code]')\" title='Code' /></i>");
    print("<i class='p-1 fa fa-link' id='BBCode' name='Url' style='height:20px; width:20px;' onclick=\"bbcomment('[url]', '[/url]')\"	title='Link' /></i>");
    print("<i class='p-1 fa fa-bolt' id='BBCode' name='scroller' style='height:20px; width:20px;'	onclick=\"bbcomment('[df]', '[/df]')\" title='scroller' /></i>");
    print("<i class='p-1 fa fa-arrow-left' id='BBCode' name='Align Leftt' style='height:20px; width:20px;' onclick=\"bbcomment('[align=left]','[/align]')\" title='Align Left' /></i>");
    print("<i class='p-1 fa fa-align-center' id='BBCode' name='Align Center' style='height:20px; width:20px;' onclick=\"bbcomment('[align=center]','[/align]')\" title='Align Center' /></i>");
    print("<i class='p-1 fa fa-arrow-right' id='BBCode' name='Align Right' style='height:20px; width:20px;'	onclick=\"bbcomment('[align=right]','[/align]')\" title='Align Right' /></i>");
    print("<i class='p-1 fa fa-picture-o' id='BBCode' name='Image' style='height:20px; width:20px;' onclick=\"bbcomment('[img]', '[/img]')\" title='Image' /></i>");
    print("<i class='p-1 fa fa-video-camera' id='BBCode' name='Video' style='height:20px; width:20px;' onclick=\"bbcomment('[video]', '[/video]')\"  title='Video' /></i>");
    print("<i class='p-1 fa fa-ban' id='BBCode' name='Hide' style='height:20px; width:20px;' onclick=\"bbcomment('[hide]','[/hide]')\"  title='Hide' /></i>");
    print("<a href='https://imgur.com/upload class='p-1'' /><img src='" . Url('assets/images/misc/bbcode_imgur.webp') . "' class='p-1' style='margin-bottom:4px;height:20px;width:20px;' title='Upload Image' /></a>");
    print("<a href='http://www.youtube.com' class='p-1' /><img src='" . Url('assets/images/misc/bbcode_youtube.webp') . "' class='p-1'  style='margin-bottom:4px;height:20px;width:20px;' title='Youtube' /></a>");
    
    // Smiley
    print("<a data-bs-toggle='collapse' data-bs-target='#collapseExample' aria-expanded='false' aria-controls='collapseExample'><img class='p-1' style='margin-bottom: 4px' src='" . Url('assets/images/smilies/grin.webp') . "' alt='' /></a>&nbsp;&nbsp;");
    
    // colour
    print("<select name='color' onChange='bbcouleur(this.value);' title='Colour'>");
    print("<option value='0' name='color'>Colour</option>");
    print("<option value='#000000' style='BACKGROUND-COLOR:#000000'>Black</option>");
    print("<option value='#686868' style='BACKGROUND-COLOR:#686868'>Grey</option>");
    print("<option value='#708090' style='BACKGROUND-COLOR:#708090'>Dark Grey</option>");
    print("<option value='#C0C0C0' style='BACKGROUND-COLOR:#C0C0C0'>Light Grey</option>");
    print("<option value='#FFFFFF' style='BACKGROUND-COLOR:#FFFFFF'>White</option>");
    print("<option value='#FFFFE0' style='BACKGROUND-COLOR:#FFFFE0'>Beech</option>");
    print("<option value='#880000' style='BACKGROUND-COLOR:#880000'>Dark Red</option>");
    print("<option value='#B82428' style='BACKGROUND-COLOR:#B82428'>Light Red</option>");
    print("<option value='#FF0000' style='BACKGROUND-COLOR:#FF0000'>Red</option>");
    print("<option value='#FF1490' style='BACKGROUND-COLOR:#FF1490'>Dark Pink</option>");
    print("<option value='#FF68B0' style='BACKGROUND-COLOR:#FF68B0'>Pink</option>");
    print("<option value='#FFC0C8' style='BACKGROUND-COLOR:#FFC0C8'>Light Pink</option>");
    print("<option value='#FF4400' style='BACKGROUND-COLOR:#FF4400'>Dark Orange</option>");
    print("<option value='#FF6448' style='BACKGROUND-COLOR:#FF6448'>Redish Orange</option>");
    print("<option value='#FFA800' style='BACKGROUND-COLOR:#FFA800'>Orange</option>");
    print("<option value='#FFD800' style='BACKGROUND-COLOR:#FFD800'>Dark Yellow</option>");
    print("<option value='#cfbc38' style='BACKGROUND-COLOR:#cfbc38'>Yellow</option>");
    print("<option value='#FF00FF' style='BACKGROUND-COLOR:#FF00FF'>Light Purple</option>");
    print("<option value='#C01480' style='BACKGROUND-COLOR:#C01480'>Dark Purple</option>");
    print("<option value='#B854D8' style='BACKGROUND-COLOR:#B854D8'>Dark Violet</option>");
    print("<option value='#D8A0D8' style='BACKGROUND-COLOR:#D8A0D8'>Light Violet</option>");
    print("<option value='#000080' style='BACKGROUND-COLOR:#000080'>Darkest Blue</option>");
    print("<option value='#0000FF' style='BACKGROUND-COLOR:#0000FF'>Dark Blue</option>");
    print("<option value='#2090FF' style='BACKGROUND-COLOR:#2090FF'>Ble</option>");
    print("<option value='#00BCFF' style='BACKGROUND-COLOR:#00BCFF'>Light Blue</option>");
    print("<option value='#B0E0E8' style='BACKGROUND-COLOR:#B0E0E8'>Faint Blue</option>");
    print("<option value='#A02828' style='BACKGROUND-COLOR:#A02828'>Brown</option>");
    print("<option value='#F0A460' style='BACKGROUND-COLOR:#F0A460'>Brown Creme</option>");
    print("<option value='#D0B488' style='BACKGROUND-COLOR:#D0B488'>Light Brown</option>");
    print("<option value='#B8B868' style='BACKGROUND-COLOR:#B8B868'>Brown/Green</option>");
    print("<option value='#008000' style='BACKGROUND-COLOR:#008000'>Dark Green</option>");
    print("<option value='#30CC30' style='BACKGROUND-COLOR:#30CC30'>Green</option>");
    print("<option value='#00FF80' style='BACKGROUND-COLOR:#00FF80'>Light Green</option>");
    print("<option value='#98FC98' style='BACKGROUND-COLOR:#98FC98'>Light Lime</option>");
    print("<option value='#98CC30' style='BACKGROUND-COLOR:#98CC30'>Dark Lime</option>");
    print("<option value='#40E0D0' style='BACKGROUND-COLOR:#40E0D0'>Turquois</option>");
    print("<option value='#20B4A8' style='BACKGROUND-COLOR:#20B4A8'>Aquarium</option></select>");
    
    // Style
    print("<select name='font' onChange='bbfont(this.value);' title='Style'>");
    print("<option value='0' name='font'>Style</option><option value='Arial' style='font-family: Arial;'>Arial</option>");
    print("<option value='Comic Sans MS' style='font-family: Comic Sans MS;'>Comic</option><option value='Trebuchet MS' style='font-family: Trebuchet MS;'>Trebuchet</option>");
    print("<option value='Courier New' style='font-family: Courier New;'>Courier</option><option value='Georgia' style='font-family: Georgia;'>Georgia</option>");
    print("<option value='Impact' style='font-family: Impact;'>Impact</option><option value='Lucida Sans Unicode' style='font-family: Lucida Sans Unicode;'>Lucida</option>");
    print("<option value='Microsoft Sans Serif' style='font-family: Microsoft Sans Serif;'>Microsoft</option>");
    print("<option value='Tahoma' style='font-family:Tahoma;'>Tahoma</option><option value='Times New Roman' style='font-family:Times New Roman;'>Roman</option>");
    print("<option value='Verdana' style='font-family:Verdana;'>Verdana</option><option value='Palatino Linotype' style='font-family:Palatino Linotype;'>Palatino</option>");
    print("<option value='Ravie' style='font-family:Ravie;'>Ravie</option><option value='WESTERN' style='font-family:WESTERN;'>Western</option>");
    print("<option value='Amerika' style='font-family:Amerika;'>Amerika</option><option value='Goudy Old Style' style='font-family:Goudy Old Style;'>Goudy</option>");
    print("<option value='Papyrus' style='font-family: Papyrus;'>Papyrus</option><option value='Brush Script MT' style='font-family:Brush Script MT;'>Brush</option></select>");
    
    // Size
    print("<select name='size' onchange='bbsize(this.value);' title='Size'><option value='0' name='size'>Size &nbsp;&nbsp;</option>");
    print("<option value='1'>1x</option><option value='2'>2x</option><option value='3'>3x</option><option value='4'>4x</option>");
    print("<option value='5'>5x</option><option value='6'>6x</option><option value='7'>7x</option></select>"); ?>

    </div>
    </div>

    <div class="container">
    <div class="row justify-content-md-center">
    <div class="collapse" id="collapseExample">
    <div class="text-center"><?php
        
        // Smiliews/emoji
        global $emoji;
        foreach($emoji as $code => $url) { ?>
            <a title="<?= $url ?>" onclick="SmileIT('<?= $code ?>','<?= $form ?>','<?= $name ?>')"><?= $code ?></a>
            <?php
        } ?>

    </div>
    </div>
    </div>
    </div>

	<div class="row justify-content-md-center">
    <div class="col-10" style="width:100%;">
	    <textarea class="form-control" id="<?= $name ?>" name="<?= $name ?>" rows="13"><?= $content ?></textarea>
    </div>
	</div>

	<div class="row justify-content-md-center">
    <div class="col-10"> <?php

        // Refresh And Preview Button
        print("<center><input type='reset' class='btn btn-sm ttbtn' value='Refresh' />&nbsp;<input type='button' class='btn btn-sm ttbtn' value='Preview' onClick='visualisation()' /></center><br>");
        
        // Creation of the Preview Area
        print("<div id='previsualisation' width='200px' height='200px'></div><br>");
    ?>
	</div>
    </div>
<?php
}

function shoutbbcode($form, $name, $content = "")
{
    require "assets/js/BBTag.js"; ?>
    
    <div class="container">
    <div class="row justify-content-md-center">
    <div class="col-12 d-flex justify-content-center"> <?php

    // BBcode
    print("<i class='p-1 fa fa-bold' id='BBCode' name='Bold' style='height:20px; width:20px;' onclick=\"bbcomment('[b]', '[/b]')\" title='Bold' /></i>");
    print("<i class='p-1 fa fa-italic' id='BBCode' name='Italic' style='height:20px; width:20px;' onclick=\"bbcomment('[i]', '[/i]')\" title='Italic' /></i>");
    print("<i class='p-1 fa fa-underline' id='BBCode' name='Highlight' style='height:20px; width:20px;' onclick=\"bbcomment('[u]', '[/u]')\" title='Highlight'/></i>");
    print("<i class='p-1 fa fa-strikethrough' id='BBCode' name='Strike' style='height:20px; width:20px;' onclick=\"bbcomment('[s]', '[/s]')\" title='Strike' /></i>");
    print("<i class='p-1 fa fa-list' id='BBCode' name='List' style='height:20px; width:20px;' onclick=\"bbcomment('[list]', '[/list]')\" title='List'	/></i>");
    print("<i class='p-1 fa fa-quote-right' id='BBCode' name='Quote' style='height:20px; width:20px;'	onclick=\"bbcomment('[quote]', '[/quote]')\" title='Quote'	/></i>");
    print("<i class='p-1 fa fa-code' id='BBCode' name='Code'  style='height:20px; width:20px;' onclick=\"bbcomment('[code]', '[/code]')\" title='Code' /></i>");
    print("<i class='p-1 fa fa-link' id='BBCode' name='Url' style='height:20px; width:20px;' onclick=\"bbcomment('[url]', '[/url]')\"	title='Link' /></i>");
    print("<i class='p-1 fa fa-picture-o' id='BBCode' name='Image' style='height:20px; width:20px;' onclick=\"bbcomment('[img]', '[/img]')\" title='Image' /></i>");
    print("<i class='p-1 fa fa-bolt' id='BBCode' name='scroller' style='height:20px; width:20px;'	onclick=\"bbcomment('[df]', '[/df]')\" title='scroller' /></i>");
    print("<i class='p-1 fa fa-arrow-left' id='BBCode' name='Align Leftt' style='height:20px; width:20px;' onclick=\"bbcomment('[align=left]','[/align]')\" title='Align Left' /></i>");
    print("<i class='p-1 fa fa-align-center' id='BBCode' name='Align Center' style='height:20px; width:20px;' onclick=\"bbcomment('[align=center]','[/align]')\" title='Align Center' /></i>");
    print("<i class='p-1 fa fa-arrow-right' id='BBCode' name='Align Right' style='height:20px; width:20px;'	onclick=\"bbcomment('[align=right]','[/align]')\" title='Align Right' /></i>");
    print("<a href='https://imgur.com/upload' target='_blank' class='p-1' style=\"background: url('" . Url('assets/images/misc/bbcode_imgur.webp') . "');  height:20px; width:20px;\" title='Upload Image' /></a>");
    print("<a href='http://www.youtube.com'	target='_blank' class='p-1'	style=\"background: url('" . Url('assets/images/misc/bbcode_youtube.webp') . "');  height:20px; width:20px;\" title='YouTube' /></a>");
    
    // Smilies
    print("<a data-bs-toggle='collapse' data-bs-target='#collapseExample' aria-expanded='false' aria-controls='collapseExample'><img <img style='margin-bottom:16px;height:20px; width:20px;' src='" . Url('assets/images/smilies/grin.webp') . "' alt='' /></a>&nbsp;&nbsp;"); 
    
    // History & Staff
    echo "<a href=" . Url('shoutbox/history') . "><small><b>" . LANG["HISTORY"] . "</b></small></a>&nbsp;&nbsp;";

    // Choose the colour
    print("<select name='color' style='padding-bottom:3px;height:24px; width:100px;' onChange='bbcouleur(this.value);' title='Colour'>");
    print("<option value='0' name='color'>Colour</option>");
    print("<option value='#000000' style='BACKGROUND-COLOR:#000000'>Black</option>");
    print("<option value='#686868' style='BACKGROUND-COLOR:#686868'>Grey</option>");
    print("<option value='#708090' style='BACKGROUND-COLOR:#708090'>Dark Grey</option>");
    print("<option value='#C0C0C0' style='BACKGROUND-COLOR:#C0C0C0'>Light Grey</option>");
    print("<option value='#FFFFFF' style='BACKGROUND-COLOR:#FFFFFF'>White</option>");
    print("<option value='#FFFFE0' style='BACKGROUND-COLOR:#FFFFE0'>Beech</option>");
    print("<option value='#880000' style='BACKGROUND-COLOR:#880000'>Dark Red</option>");
    print("<option value='#B82428' style='BACKGROUND-COLOR:#B82428'>Light Red</option>");
    print("<option value='#FF0000' style='BACKGROUND-COLOR:#FF0000'>Red</option>");
    print("<option value='#FF1490' style='BACKGROUND-COLOR:#FF1490'>Dark Pink</option>");
    print("<option value='#FF68B0' style='BACKGROUND-COLOR:#FF68B0'>Pink</option>");
    print("<option value='#FFC0C8' style='BACKGROUND-COLOR:#FFC0C8'>Light Pink</option>");
    print("<option value='#FF4400' style='BACKGROUND-COLOR:#FF4400'>Dark Orange</option>");
    print("<option value='#FF6448' style='BACKGROUND-COLOR:#FF6448'>Redish Orange</option>");
    print("<option value='#FFA800' style='BACKGROUND-COLOR:#FFA800'>Orange</option>");
    print("<option value='#FFD800' style='BACKGROUND-COLOR:#FFD800'>Dark Yellow</option>");
    print("<option value='#cfbc38' style='BACKGROUND-COLOR:#cfbc38'>Yellow</option>");
    print("<option value='#FF00FF' style='BACKGROUND-COLOR:#FF00FF'>Light Purple</option>");
    print("<option value='#C01480' style='BACKGROUND-COLOR:#C01480'>Dark Purple</option>");
    print("<option value='#B854D8' style='BACKGROUND-COLOR:#B854D8'>Dark Violet</option>");
    print("<option value='#D8A0D8' style='BACKGROUND-COLOR:#D8A0D8'>Light Violet</option>");
    print("<option value='#000080' style='BACKGROUND-COLOR:#000080'>Darkest Blue</option>");
    print("<option value='#0000FF' style='BACKGROUND-COLOR:#0000FF'>Dark Blue</option>");
    print("<option value='#2090FF' style='BACKGROUND-COLOR:#2090FF'>Ble</option>");
    print("<option value='#00BCFF' style='BACKGROUND-COLOR:#00BCFF'>Light Blue</option>");
    print("<option value='#B0E0E8' style='BACKGROUND-COLOR:#B0E0E8'>Faint Blue</option>");
    print("<option value='#A02828' style='BACKGROUND-COLOR:#A02828'>Brown</option>");
    print("<option value='#F0A460' style='BACKGROUND-COLOR:#F0A460'>Brown Creme</option>");
    print("<option value='#D0B488' style='BACKGROUND-COLOR:#D0B488'>Light Brown</option>");
    print("<option value='#B8B868' style='BACKGROUND-COLOR:#B8B868'>Brown/Green</option>");
    print("<option value='#008000' style='BACKGROUND-COLOR:#008000'>Dark Green</option>");
    print("<option value='#30CC30' style='BACKGROUND-COLOR:#30CC30'>Green</option>");
    print("<option value='#00FF80' style='BACKGROUND-COLOR:#00FF80'>Light Green</option>");
    print("<option value='#98FC98' style='BACKGROUND-COLOR:#98FC98'>Light Lime</option>");
    print("<option value='#98CC30' style='BACKGROUND-COLOR:#98CC30'>Dark Lime</option>");
    print("<option value='#40E0D0' style='BACKGROUND-COLOR:#40E0D0'>Turquois</option>");
    print("<option value='#20B4A8' style='BACKGROUND-COLOR:#20B4A8'>Aquarium</option></select>");
    ?>
    </div>

    <div class="container">
    <div class="row justify-content-md-center">
    <div class="collapse" id="collapseExample">
    <div class="text-center"> <?php

        // Smiliews/emoji
        global $emoji;
        foreach($emoji as $code => $url) {
            ?>
            <a title="<?= $url ?>" onclick="SmileIT('<?= $code ?>','<?= $form ?>','<?= $name ?>')"><?= $code ?></a>
            <?php
        } ?>

    </div>
    </div>
    </div>
    </div>

    <div class="col-md-11">
        <input id="message" class="form-control" type='text' size='100%' name="<?= $name ?>"><?= $content ?>
    </div>
    <div class="col-md-1">
        <center><input type='submit' name='submit' value='<?= LANG["SUBMIT"] ?>' class='btn btn-sm ttbtn' /></center>
    </div>

    </div>
    </div> <?php
}