<?php

namespace App\Controllers\Admin;

use App\Libs\Controller;

/*
* Config Controller
*/
class Adminconfig extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
        if ($this->current_user->class < CONFIG['_MODERATOR']) {
            showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
        }
    }

    // Check Admin !!!
    public function check()
    {
    
        if (!in_array($this->current_user->username, CONFIG['_OWNERS'])) {
            Url(Url('logout'), true);
        }
    }

    // Config Default Page
    public function index()
    {
    
        if (!in_array($this->current_user->username, CONFIG['_OWNERS'])) {
            Url(Url('logout'), true);
        }

        // Data For View
        $data = [
            'page_title' => LANG['CONFIG']
        ];

        // Load View
        $this->view('config', $data, 'admin');
    }

    // Check Input For True/False
    public function checkiftrue($var)
    {
        $check = $var == 'true' ? true : false;
        return $check;
    }

    // Config Form Submit
    public function submit()
    {
        // Check Admin !!!
        $this->check();
        
        // Check Input
        if (!$_POST) {
            Url(Url('logout'), true);
        }

        // Data For View
        $data = array (
            'SITENAME' => $_POST['SITENAME'],
            '_SITEDESC' => $_POST['_SITEDESC'],
            'SITEEMAIL' => $_POST['SITEEMAIL'],
            'SITENOTICEON' => $this->checkiftrue($_POST['SITENOTICEON']),
            'SITENOTICE' => $_POST['SITENOTICE'],

            'SITE_ONLINE' => $this->checkiftrue($_POST['SITE_ONLINE']),
            'OFFLINEMSG' => $_POST['OFFLINEMSG'],
            'WELCOMEPM_ON' => $this->checkiftrue($_POST['WELCOMEPM_ON']),
            'WELCOMEPM_MSG' => $_POST['WELCOMEPM_MSG'],
            'UPLOADRULES' => $_POST['UPLOADRULES'],

            'CAPTCHA_ON' => $this->checkiftrue($_POST['CAPTCHA_ON']),
            'CAPTCHA_KEY' => $_POST['CAPTCHA_KEY'],
            'CAPTCHA_SECRET' => $_POST['CAPTCHA_SECRET'],
            'CSRF_Token' => $this->checkiftrue($_POST['CSRF_Token']),
            'DEFAULTLANG' => $_POST['DEFAULTLANG'],
            'DEFAULTTHEME' => $_POST['DEFAULTTHEME'],

            'MEMBERSONLY' => $this->checkiftrue($_POST['MEMBERSONLY']),
            'MEMBERSONLY_WAIT' => $this->checkiftrue($_POST['MEMBERSONLY_WAIT']),
            'ALLOWEXTERNAL' => $this->checkiftrue($_POST['ALLOWEXTERNAL']),
            'UPLOADERSONLY' => $this->checkiftrue($_POST['UPLOADERSONLY']),
            'INVITEONLY' => $this->checkiftrue($_POST['INVITEONLY']),
            'ENABLEINVITES' => $this->checkiftrue($_POST['ENABLEINVITES']),
            'CONFIRMEMAIL' => $this->checkiftrue($_POST['CONFIRMEMAIL']),
            'ACONFIRM' => $this->checkiftrue($_POST['ACONFIRM']),
            'ANONYMOUSUPLOAD' => $this->checkiftrue($_POST['ANONYMOUSUPLOAD']),
            'UPLOADSCRAPE' => $this->checkiftrue($_POST['UPLOADSCRAPE']),
            'FORUMS' => $this->checkiftrue($_POST['FORUMS']),
            'FORUMS_GUESTREAD' => $this->checkiftrue($_POST['FORUMS_GUESTREAD']),
            'FORCETHANKS' => $this->checkiftrue($_POST['FORCETHANKS']),
            'ALLOWLIKES' => $this->checkiftrue($_POST['ALLOWLIKES']),
            'REQUESTSON' => $this->checkiftrue($_POST['REQUESTSON']),

            'LEFTNAV' => $this->checkiftrue($_POST['LEFTNAV']),
            'RIGHTNAV' => $this->checkiftrue($_POST['RIGHTNAV']),
            'MIDDLENAV' => $this->checkiftrue($_POST['MIDDLENAV']),
            'SHOUTBOX' => $this->checkiftrue($_POST['SHOUTBOX']),
            'NEWSON' => $this->checkiftrue($_POST['NEWSON']),
            'DONATEON' => $this->checkiftrue($_POST['DONATEON']),
            'DISCLAIMERON' => $this->checkiftrue($_POST['DISCLAIMERON']),
            'FORUMONINDEX' => $this->checkiftrue($_POST['FORUMONINDEX']),
            'LATESTFORUMPOSTONINDEX' => $this->checkiftrue($_POST['LATESTFORUMPOSTONINDEX']),
            'IPCHECK' => $this->checkiftrue($_POST['IPCHECK']),
            'YOU_TUBE' => $this->checkiftrue($_POST['YOU_TUBE']),
            'FREELEECHGBON' => $this->checkiftrue($_POST['FREELEECHGBON']),
            'FREELEECHGB' => (int) $_POST['FREELEECHGB'],

            'ACCOUNTMAX' => (int) $_POST['ACCOUNTMAX'],
            'MAXUSERS' => (int) $_POST['MAXUSERS'],
            'MAXUSERSINVITE' => (int) $_POST['MAXUSERSINVITE'],
            'CURRENCYSYMBOL' => $_POST['CURRENCYSYMBOL'],
            'BONUSPERTIME' => (float) $_POST['BONUSPERTIME'],
            'ADDBONUS' => (int) $_POST['ADDBONUS'],

            'CACHE_TYPE' => $_POST['CACHE_TYPE'],
            'MEMCACHE_HOST' => $_POST['MEMCACHE_HOST'],
            'MEMCACHE_PORT' => (int) $_POST['MEMCACHE_PORT'],

            'PEERLIMIT' => (int) $_POST['PEERLIMIT'],
            'AUTOCLEANINTERVAL' => (int) $_POST['AUTOCLEANINTERVAL'],
            'ANNOUNCEINTERVAL' => (int) $_POST['ANNOUNCEINTERVAL'],
            'SIGNUPTIMEOUT' => (int) $_POST['SIGNUPTIMEOUT'],
            'MAXDEADTORRENTTIMEOUT' => (int) $_POST['MAXDEADTORRENTTIMEOUT'],
            'LOGCLEAN' => (int) $_POST['LOGCLEAN'],

            'mail_type' => $_POST['mail_type'],
            'mail_smtp_host' => $_POST['mail_smtp_port'],
            'mail_smtp_port' => (int) $_POST['mail_smtp_port'],
            'mail_smtp_ssl' => $this->checkiftrue($_POST['mail_smtp_ssl']),
            'mail_smtp_auth' => $this->checkiftrue($_POST['mail_smtp_auth']),
            'mail_smtp_user' => $_POST['mail_smtp_user'],
            'mail_smtp_pass' => $_POST['mail_smtp_pass'],

            'RATIOWARNENABLE' => $this->checkiftrue($_POST['RATIOWARNENABLE']),
            'RATIOWARNMINRATIO' => (float) $_POST['RATIOWARNMINRATIO'],
            'RATIOWARN_MINGIGS' => (int) $_POST['RATIOWARN_MINGIGS'],
            'RATIOWARN_DAYSTOWARN' => (int) $_POST['RATIOWARN_DAYSTOWARN'],

            'CLASS_WAIT' => (int) $_POST['CLASS_WAIT'],
            'GIGSA' => (int) $_POST['GIGSA'],
            'RATIOA' => (float) $_POST['RATIOA'],
            'A_WAIT' => (int) $_POST['A_WAIT'],
            'GIGSB' => (int) $_POST['GIGSB'],
            'RATIOB' => (float) $_POST['RATIOB'],
            'B_WAIT' => (int) $_POST['B_WAIT'],
            'GIGSC' => (int) $_POST['GIGSC'],
            'RATIOC' => (float) $_POST['RATIOC'],
            'C_WAIT' => (int) $_POST['C_WAIT'],
            'GIGSD' => (int) $_POST['GIGSD'],
            'RATIOD' => (float) $_POST['RATIOD'],
            'D_WAIT' => (int) $_POST['D_WAIT'],
            
            'HNR_ON' => $this->checkiftrue($_POST['HNR_ON']),
            'HNR_DEADLINE' => (int) $_POST['HNR_DEADLINE'],
            'HNR_SEEDTIME' => (int) $_POST['HNR_SEEDTIME'],
            'HNR_WARN' => (int) $_POST['HNR_WARN'],
            'HNR_STOP_DL' => (int) $_POST['HNR_STOP_DL'],
            'HNR_BAN' => (int) $_POST['HNR_BAN'],
            );

        file_put_contents(CONFIG['APPROOT'].'/Config/settings.php', "<?php\ndefine('CONFIG', " . var_export($data, true) . ");");
        showMessage(Url('adminconfig'), LANG["ACTION_COMPLETE"]);
    }

    // Backup Form Submit
    public function backup()
    {
        // Check Admin !!!
        $this->check();

        // Get Files
        $file = CONFIG['APPROOT'].'/Config/settings.php';
        $newfile = CONFIG['APPROOT'].'/Config/settings.php.bak';
        
        // Create Backup
        if (!copy($file, $newfile)) {
            showMessage(Url('adminconfig'), LANG["ADDED_FAILED"]);
        }
        showMessage(Url('adminconfig'), LANG["SUCCESS"]);
    }

}