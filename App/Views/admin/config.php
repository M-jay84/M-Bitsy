<?php block_begin($page_title) ?>
<form method="post" action="<?= Url('adminconfig/submit') ?>">
    
  <div class="rounded p-4">
    <p class='text-center'>
      <b><?= LANG["Settings"]; ?></b>
      To Allow User, add username to CONFIG['_OWNERS'] setting manually at app/config/settings.php<br>
      Like your Settings ? Make a backup before changing <a href='<?= Url('adminconfig/backup') ?>'> Here</a>
    </p>
  </div><br>

  <div class="row rounded m-0 p-2">
    <p class="text-center"><b><?= LANG["Details"]; ?></b></p>
    <div class="col-md-2">
      <label for="SITENAME">Site Name:</label><br>
      <input type="text" class="form-control fullwidth" id="SITENAME" name="SITENAME" value="<?= CONFIG['SITENAME'] ?>"><br>
      <label for="SITEEMAIL">Site Email:</label><br>
      <input type="text" class="form-control fullwidth" id="SITEEMAIL" name="SITEEMAIL" value="<?= CONFIG['SITEEMAIL'] ?>"><br>
    </div>
    <div class="col-md-2">
      <label for="_SITEDESC">Site Description:</label><br>
      <textarea class="form-control fullwidth" id="_SITEDESC" name="_SITEDESC" style="height:130px;"><?= CONFIG['_SITEDESC'] ?></textarea><br>
    </div>
    <div class="col-md-2">
      <label for="DEFAULTLANG">Default Language:</label><br>
      <input type="text" class="form-control fullwidth" id="DEFAULTLANG" name="DEFAULTLANG" value="<?= CONFIG['DEFAULTLANG'] ?>"><br>
      <label for="DEFAULTTHEME">Default Theme:</label><br>
      <input type="text" class="form-control fullwidth" id="DEFAULTTHEME" name="DEFAULTTHEME" value="<?= CONFIG['DEFAULTTHEME'] ?>"><br>
    </div>
    <div class="col-md-2">
      <label for="CSRF_Token">CSRF Token on/off :</label><br>
      <?php $checked = CONFIG['CSRF_Token'] == 1;
      print("<input name='CSRF_Token' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='CSRF_Token' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
      <label for="MEMBERSONLY">Members Only:</label><br>
      <?php $checked = CONFIG['MEMBERSONLY'] == 1;
      print("<input name='MEMBERSONLY' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='MEMBERSONLY' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
    </div>
    <div class="col-md-2">
      <label for="INVITEONLY">Invite Only:</label><br>
      <?php $checked = CONFIG['INVITEONLY'] == 1;
      print("<input name='INVITEONLY' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='INVITEONLY' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
      <label for="ENABLEINVITES">Enable Invites:</label><br>
      <?php $checked = CONFIG['ENABLEINVITES'] == 1;
      print("<input name='ENABLEINVITES' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='ENABLEINVITES' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
    </div>
    <div class="col-md-2">
      <label for="CONFIRMEMAIL">Confirm Email:</label><br>
      <?php $checked = CONFIG['CONFIRMEMAIL'] == 1;
      print("<input name='CONFIRMEMAIL' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='CONFIRMEMAIL' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
      <label for="ACONFIRM">Admin Confirm:</label><br>
      <?php $checked = CONFIG['ACONFIRM'] == 1;
      print("<input name='ACONFIRM' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='ACONFIRM' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
    </div>
  </div><br>

  <div class="row rounded m-0 p-2">
    <p class="text-center"><b><?= LANG["Online, Welcome & Captcha"]; ?></b></p>
    <div class="col-md-2">
      <label for="OFFLINEMSG">Site Online:</label><br>
      <?php $checked = CONFIG['SITE_ONLINE'] == 1;
      print("<input name='SITE_ONLINE' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='SITE_ONLINE' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
      <label for="OFFLINEMSG">Offline Messages:</label><br>
      <textarea class="form-control" id="OFFLINEMSG" name="OFFLINEMSG" style="height:130px;"><?= CONFIG['OFFLINEMSG'] ?></textarea><br>
    </div>
    <div class="col-md-2">
    <label for="WELCOMEPM_ON">Welcome PM On:</label><br>
      <?php $checked = CONFIG['WELCOMEPM_ON'] == 1;
      print("<input name='WELCOMEPM_ON' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='WELCOMEPM_ON' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
    <label for="WELCOMEPM_MSG">Welcome PM Message:</label><br>
      <textarea class="form-control fullwidth" name="WELCOMEPM_MSG" style="height:130px;"><?= CONFIG['WELCOMEPM_MSG'] ?></textarea><br>
    </div>
    <div class="col-md-2">
       <label for="SITENOTICEON">Site Notice on/off:</label><br>
      <?php $checked = CONFIG['SITENOTICEON'] == true;
      print("<input name='SITENOTICEON' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='SITENOTICEON' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
      <label for="SITENOTICE">Site Notice:</label><br>
      <textarea class="form-control fullwidth" id="SITENOTICE" name="SITENOTICE" style="height:130px;"><?= CONFIG['SITENOTICE'] ?></textarea><br>
    </div>
    <div class="col-md-2">
      <label for="CAPTCHA_ON">Google Captcha on/off :</label><br>
      <?php $checked = CONFIG['CAPTCHA_ON'] == 1;
      print("<input name='CAPTCHA_ON' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='CAPTCHA_ON' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
      <label for="CAPTCHA_KEY">Captcha Key:</label><br>
      <input type="text" class="form-control" id="CAPTCHA_KEY" name="CAPTCHA_KEY" value="<?= CONFIG['CAPTCHA_KEY'] ?>"><br>
      <label for="CAPTCHA_SECRET">Captcha Secret:</label><br>
      <input type="text" class="form-control" id="CAPTCHA_SECRET" name="CAPTCHA_SECRET" value="<?= CONFIG['CAPTCHA_SECRET'] ?>"><br>
    </div>
    <div class="col-md-2">
      <label for="SHOUTBOX">Shoutbox on/off:</label><br>
      <?php $checked = CONFIG['SHOUTBOX'] == 1;
      print("<input name='SHOUTBOX' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='SHOUTBOX' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
      <label for="NEWSON">News on/off:</label><br>
      <?php $checked = CONFIG['NEWSON'] == 1;
      print("<input name='NEWSON' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='NEWSON' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
      <label for="FORUMS">Forums on/off:</label><br>
      <?php $checked = CONFIG['FORUMS'] == 1;
      print("<input name='FORUMS' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='FORUMS' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
    </div>
    <div class="col-md-2">
      <label for="ALLOWEXTERNAL">Allow External:</label><br>
      <?php $checked = CONFIG['ALLOWEXTERNAL'] == 1;
      print("<input name='ALLOWEXTERNAL' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='ALLOWEXTERNAL' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
      <label for="UPLOADERSONLY">Uploader Only:</label><br>
      <?php $checked = CONFIG['SHOUTBOX'] == 1;
      print("<input name='UPLOADERSONLY' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='UPLOADERSONLY' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
      <label for="ANONYMOUSUPLOAD">Anon Uploads:</label><br>
      <?php $checked = CONFIG['ANONYMOUSUPLOAD'] == 1;
      print("<input name='ANONYMOUSUPLOAD' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='ANONYMOUSUPLOAD' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
    </div>
  </div><br>

  <div class="row rounded m-0 p-2">
    <p class="text-center"><b><?= LANG["Optons"]; ?></b></p>
    <div class="col-md-2">
      <label for="UPLOADSCRAPE">Upload Scrape:</label><br>
      <?php $checked = CONFIG['UPLOADSCRAPE'] == 1;
      print("<input name='UPLOADSCRAPE' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='UPLOADSCRAPE' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
      <label for="FORCETHANKS">Force Thanks:</label><br>
      <?php $checked = CONFIG['FORCETHANKS'] == 1;
      print("<input name='FORCETHANKS' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='FORCETHANKS' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
      <label for="ALLOWLIKES">Allow Likes:</label><br>
      <?php $checked = CONFIG['ALLOWLIKES'] == 1;
      print("<input name='ALLOWLIKES' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='ALLOWLIKES' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
    </div>
    <div class="col-md-2">
      <label for="REQUESTSON">Request On:</label><br>
      <?php $checked = CONFIG['REQUESTSON'] == 1;
      print("<input name='REQUESTSON' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='REQUESTSON' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
      <label for="DONATEON">Donate on/off:</label><br>
      <?php $checked = CONFIG['DONATEON'] == 1;
      print("<input name='DONATEON' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='DONATEON' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
      <label for="DISCLAIMERON">Disclaimer on/off:</label><br>
      <?php $checked = CONFIG['DISCLAIMERON'] == 1;
      print("<input name='DISCLAIMERON' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='DISCLAIMERON' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
    </div>
    <div class="col-md-2">
      <label for="FORUMONINDEX">Forum On Index:</label><br>
      <?php $checked = CONFIG['FORUMONINDEX'] == 1;
      print("<input name='FORUMONINDEX' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='FORUMONINDEX' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
      <label for="FORUMS_GUESTREAD">Forum Guest Read:</label><br>
      <?php $checked = CONFIG['FORUMS_GUESTREAD'] == 1;
      print("<input name='FORUMS_GUESTREAD' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='FORUMS_GUESTREAD' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
      <label for="FORUMONINDEX">Latest Forum Post On Index:</label><br>
      <?php $checked = CONFIG['LATESTFORUMPOSTONINDEX'] == 1;
      print("<input name='LATESTFORUMPOSTONINDEX' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='LATESTFORUMPOSTONINDEX' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
    </div>
    <div class="col-md-2">
      <label for="IPCHECK">IP Limit To 1:</label><br>
      <?php $checked = CONFIG['IPCHECK'] == 1;
      print("<input name='IPCHECK' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='IPCHECK' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
      <label for="YOU_TUBE">Youtube:</label><br>
      <?php $checked = CONFIG['YOU_TUBE'] == 1;
      print("<input name='YOU_TUBE' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='YOU_TUBE' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
    </div>
    <div class="col-md-2">
      <label for="FREELEECHGBON">Free Leech On:</label><br>
      <?php $checked = CONFIG['FREELEECHGBON'] == 1;
      print("<input name='FREELEECHGBON' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='FREELEECHGBON' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
      <label for="FREELEECHGB">Free Leech GB:</label><br>
      <input type="text" class="form-control" id="FREELEECHGB" name="FREELEECHGB" value="<?= CONFIG['FREELEECHGB'] ?>"><br>
    </div>
    <div class="col-md-2">
      <label for="UPLOADRULES">Upload Rules:</label><br>
      <textarea class="form-control fullwidth" name="UPLOADRULES" style="height:190px;"><?= CONFIG['UPLOADRULES'] ?></textarea><br>
    </div>
  </div><br>

  <div class="row rounded m-0 p-2">
    <p class="text-center"><b><?= LANG["Max User, Bonus, Cache, Limits"]; ?></b></p>
    <div class="col-md-2">
      <label for="ACCOUNTMAX">Account Max:</label><br>
      <input type="text" class="form-control" id="ACCOUNTMAX" name="ACCOUNTMAX" value="<?= CONFIG['ACCOUNTMAX'] ?>"><br>
      <label for="MAXUSERS">Max Users:</label><br>
      <input type="text" class="form-control" id="MAXUSERS" name="MAXUSERS" value="<?= CONFIG['MAXUSERS'] ?>"><br>
      <label for="MAXUSERSINVITE">Max Users Invited:</label><br>
      <input type="text" class="form-control" id="MAXUSERSINVITE" name="MAXUSERSINVITE" value="<?= CONFIG['MAXUSERSINVITE'] ?>"><br>
    </div>
    <div class="col-md-2">
      <label for="CURRENCYSYMBOL">Currency Symbol:</label><br>
      <input type="text" class="form-control" id="CURRENCYSYMBOL" name="CURRENCYSYMBOL" value="<?= CONFIG['CURRENCYSYMBOL'] ?>"><br>
      <label for="BONUSPERTIME">Add Bonus:</label><br>
      <input type="text" class="form-control" id="BONUSPERTIME" name="BONUSPERTIME" value="<?= CONFIG['BONUSPERTIME'] ?>"><br>
      <label for="ADDBONUS">Bonus Time (seconds):</label><br>
      <input type="text" class="form-control" id="ADDBONUS" name="ADDBONUS" value="<?= CONFIG['ADDBONUS'] ?>"><br>
    </div>
    <div class="col-md-2">
      <label for="CONFIG['CACHE']_TYPE">Cache Type:</label><br>
      <input type="text" class="form-control" id="CONFIG['CACHE']_TYPE" name="CONFIG['CACHE']_TYPE" value="<?= CONFIG['CACHE_TYPE'] ?>"><br>
      <label for="MEMCONFIG['CACHE']_HOST">Memcache Host:</label><br>
      <input type="text" class="form-control" id="MEMCONFIG['CACHE']_HOST" name="MEMCONFIG['CACHE']_HOST" value="<?= CONFIG['MEMCACHE_HOST'] ?>"><br>
      <label for="MEMCONFIG['CACHE']_PORT">Memcache Port:</label><br>
      <input type="text" class="form-control" id="MEMCONFIG['CACHE']_PORT" name="MEMCONFIG['CACHE']_PORT" value="<?= CONFIG['MEMCACHE_PORT'] ?>"><br>
    </div>
    <div class="col-md-2">
      <label for="PEERLIMIT">Peer Limit:</label><br>
      <input type="text" class="form-control" id="PEERLIMIT" name="PEERLIMIT" value="<?= CONFIG['PEERLIMIT'] ?>"><br>
      <label for="AUTOCLEANINTERVAL">Autoclean Interval:</label><br>
      <input type="text" class="form-control" id="AUTOCLEANINTERVAL" name="AUTOCLEANINTERVAL" value="<?= CONFIG['AUTOCLEANINTERVAL'] ?>"><br>
      <label for="ANNOUNCEINTERVAL">Announce Interval:</label><br>
      <input type="text" class="form-control" id="ANNOUNCEINTERVAL" name="ANNOUNCEINTERVAL" value="<?= CONFIG['ANNOUNCEINTERVAL'] ?>"><br>
    </div>
    <div class="col-md-2">
      <label for="SIGNUPTIMEOUT">Signup Timeout:</label><br>
      <input type="text" class="form-control" id="SIGNUPTIMEOUT" name="SIGNUPTIMEOUT" value="<?= CONFIG['SIGNUPTIMEOUT'] ?>"><br>
      <label for="MAXDEADTORRENTTIMEOUT">Dead Torrent Timeout:</label><br>
      <input type="text" class="form-control" id="MAXDEADTORRENTTIMEOUT" name="MAXDEADTORRENTTIMEOUT" value="<?= CONFIG['MAXDEADTORRENTTIMEOUT'] ?>"><br>
      <label for="LOGCLEAN">Log Clean:</label><br>
      <input type="text" class="form-control" id="LOGCLEAN" name="LOGCLEAN" value="<?= CONFIG['LOGCLEAN'] ?>"><br>
    </div>
  </div><br>
  
  <div class="row rounded m-0 p-2">
    <p class="text-center"><b><?= LANG["Email"]; ?></b></p>
    <div class="col-md-2">
      <label for="mail_type">Mail Type:</label><br>
      <input type="text" class="form-control" id="mail_type" name="mail_type" value="<?= CONFIG['mail_type'] ?>"><br>
    </div>
    <div class="col-md-2">
      <label for="mail_smtp_host">SMTP Host:</label><br>
      <input type="text" class="form-control" id="mail_smtp_host" name="mail_smtp_host" value="<?= CONFIG['mail_smtp_host'] ?>"><br>
    </div>
    <div class="col-md-2">
      <label for="mail_smtp_port">SMTP Port:</label><br>
      <input type="text" class="form-control" id="mail_smtp_port" name="mail_smtp_port" value="<?= CONFIG['mail_smtp_port'] ?>"><br>
    </div>
    <div class="col-md-1">
      <label for="mail_smtp_ssl">SMTP SSL:</label><br>
      <?php $checked = CONFIG['mail_smtp_ssl'] == 1;
      print("<input name='mail_smtp_ssl' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='mail_smtp_ssl' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
    </div>
    <div class="col-md-1">
      <label for="mail_smtp_auth">SMTP Auth:</label><br>
      <?php $checked = CONFIG['mail_smtp_auth'] == 1;
      print("<input name='mail_smtp_auth' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='mail_smtp_auth' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
    </div>
    <div class="col-md-2">
      <label for="mail_smtp_user">SMTP User:</label><br>
      <input type="text" class="form-control" id="mail_smtp_user" name="mail_smtp_user" value="<?= CONFIG['mail_smtp_user'] ?>"><br>
    </div>
    <div class="col-md-2">
      <label for="mail_smtp_pass">SMTP Pass:</label><br>
      <input type="text" class="form-control" id="mail_smtp_pass" name="mail_smtp_pass" value="<?= CONFIG['mail_smtp_pass'] ?>"><br>
    </div>
  </div><br>
  
  <div class="row rounded m-0 p-2">
    <p class="text-center"><b><?= LANG["Hit & Run"]; ?></b></p>
    <div class="col-md-2">
      <label for="HNR_ON">Hit & Run on/off:</label><br>
      <?php $checked = CONFIG['HNR_ON'] == 1;
      print("<input name='HNR_ON' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='HNR_ON' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
    </div>
    <div class="col-md-2">
      <label for="HNR_DEADLINE">Deadline:</label><br>
      <input type="text" class="form-control" id="HNR_DEADLINE" name="HNR_DEADLINE" value="<?= CONFIG['HNR_DEADLINE'] ?>"><br>
    </div>
    <div class="col-md-2">
      <label for="HNR_SEEDTIME">Seed Time:</label><br>
      <input type="text" class="form-control" id="HNR_SEEDTIME" name="HNR_SEEDTIME" value="<?= CONFIG['HNR_SEEDTIME'] ?>"><br>
    </div>
    <div class="col-md-2">
      <label for="HNR_WARN">HNR Warn:</label><br>
      <input type="text" class="form-control" id="HNR_WARN" name="HNR_WARN" value="<?= CONFIG['HNR_WARN'] ?>"><br>
    </div>
    <div class="col-md-2">
      <label for="HNR_STOP_DL">Ban DL:</label><br>
      <input type="text" class="form-control" id="HNR_STOP_DL" name="HNR_STOP_DL" value="<?= CONFIG['HNR_STOP_DL'] ?>"><br>
    </div>
    <div class="col-md-2"><label for="HNR_BAN">Ban User:</label><br>
      <input type="text" class="form-control" id="HNR_BAN" name="HNR_BAN" value="<?= CONFIG['HNR_BAN'] ?>"><br>
    </div>
  </div><br>
  
  <div class="row rounded m-0 p-2">
    <p class="text-center"><b><?= LANG["Ratio Warn"]; ?></b></p>
    <div class="col-md-3">
      <label for="RATIOWARNENABLE">Ratio Warn Enable:</label><br>
      <?php $checked = CONFIG['RATIOWARNENABLE'] == 1;
      print("<input name='RATIOWARNENABLE' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='RATIOWARNENABLE' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
    </div>
    <div class="col-md-3">
      <label for="RATIOWARNMINRATIO">Warn Min Ration:</label><br>
      <input type="text" class="form-control" id="RATIOWARNMINRATIO" name="RATIOWARNMINRATIO" value="<?= CONFIG['RATIOWARNMINRATIO'] ?>"><br>
    </div>
    <div class="col-md-3">
      <label for="RATIOWARN_MINGIGS">Warn Min Gigs:</label><br>
      <input type="text" class="form-control" id="RATIOWARN_MINGIGS" name="RATIOWARN_MINGIGS" value="<?= CONFIG['RATIOWARN_MINGIGS'] ?>"><br>
    </div>
    <div class="col-md-3">
      <label for="RATIOWARN_DAYSTOWARN">Days To Warn:</label><br>
      <input type="text" class="form-control" id="RATIOWARN_DAYSTOWARN" name="RATIOWARN_DAYSTOWARN" value="<?= CONFIG['RATIOWARN_DAYSTOWARN'] ?>"><br>
    </div>
  </div><br>
  
  <div class="row rounded m-0 p-2">
      <div class="col-md-2">
    <label for="CLASS_WAIT">Class Wait:</label><br>
      <input type="text" class="form-control" id="CLASS_WAIT" name="CLASS_WAIT" value="<?= CONFIG['CLASS_WAIT'] ?>"><br>
     </div>
    <div class="col-md-2">
      <label for="MEMBERSONLY_WAIT">Members Wait:</label><br>
      <?php $checked = CONFIG['MEMBERSONLY_WAIT'] == 1;
      print("<input name='MEMBERSONLY_WAIT' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='MEMBERSONLY_WAIT' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
    </div>
    <div class="col-md-2">
      <label for="LEFTNAV">Left Blocks on/off:</label><br>
      <?php $checked = CONFIG['LEFTNAV'] == 1;
      print("<input name='LEFTNAV' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='LEFTNAV' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
    </div>
    <div class="col-md-2">
      <label for="RIGHTNAV">Right Blocks on/off:</label><br>
      <?php $checked = CONFIG['RIGHTNAV'] == 1;
      print("<input name='RIGHTNAV' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='RIGHTNAV' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
    </div>
    <div class="col-md-2">
      <label for="MIDDLENAV">Middle Blocks on/off:</label><br>
      <?php $checked = CONFIG['MIDDLENAV'] == 1;
      print("<input name='MIDDLENAV' value='true' type='radio' " . ($checked ? " checked='checked'" : "") . " />True &nbsp;&nbsp;<input name='MIDDLENAV' value='false' type='radio' " . (!$checked ? " checked='checked'" : "") . " />False<br><br>"); ?>
    </div>
  </div><br>
  
  <div class="row rounded m-0 p-2">
    <p class="text-center"><b><?= LANG["Waiting Times"]; ?></b></p>
    <div class="col-md-3">
      <label for="GIGSA">A Gigs:</label><br>
      <input type="text" class="form-control" id="GIGSA" name="GIGSA" value="<?= CONFIG['GIGSA'] ?>"><br>
      <label for="RATIOA">A Ratio:</label><br>
      <input type="text" class="form-control" id="RATIOA" name="RATIOA" value="<?= CONFIG['RATIOA'] ?>"><br>
      <label for="A_WAIT">A Wait:</label><br>
      <input type="text" class="form-control" id="A_WAIT" name="A_WAIT" value="<?= CONFIG['A_WAIT'] ?>"><br>
    </div>
    <div class="col-md-3">
      <label for="GIGSB">B Gigs:</label><br>
      <input type="text" class="form-control" id="GIGSB" name="GIGSB" value="<?= CONFIG['GIGSB'] ?>"><br>
      <label for="RATIOB">B Ratio:</label><br>
      <input type="text" class="form-control" id="RATIOB" name="RATIOB" value="<?= CONFIG['RATIOB'] ?>"><br>
      <label for="B_WAIT">B Wait:</label><br>
      <input type="text" class="form-control" id="B_WAIT" name="B_WAIT" value="<?= CONFIG['B_WAIT'] ?>"><br>
    </div>
    <div class="col-md-3">
      <label for="GIGSC">C Gigs:</label><br>
      <input type="text" class="form-control" id="GIGSC" name="GIGSC" value="<?= CONFIG['GIGSC'] ?>"><br>
      <label for="RATIOC">C Ratio:</label><br>
      <input type="text" class="form-control" id="RATIOC" name="RATIOC" value="<?= CONFIG['RATIOC'] ?>"><br>
      <label for="C_WAIT">C Wait:</label><br>
      <input type="text" class="form-control" id="C_WAIT" name="C_WAIT" value="<?= CONFIG['C_WAIT'] ?>"><br>
    </div>
    <div class="col-md-3">
      <label for="GIGSD">D Gigs:</label><br>
      <input type="text" class="form-control" id="GIGSD" name="GIGSD" value="<?= CONFIG['GIGSD'] ?>"><br>
      <label for="RATIOD">D Ratio:</label><br>
      <input type="text" class="form-control" id="RATIOD" name="RATIOD" value="<?= CONFIG['RATIOD'] ?>"><br>
      <label for="D_WAIT">D Wait:</label><br>
      <input type="text" class="form-control" id="D_WAIT" name="D_WAIT" value="<?= CONFIG['D_WAIT'] ?>"><br>
    </div>
  </div><br>

  <p class="text-center">
    <input type="submit" class="btn btn-sm ttbtn" value="Submit">
  </p>

</form>
<?php block_end() ?>