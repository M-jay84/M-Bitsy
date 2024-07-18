<?php

namespace App\Libs;

use App\Libs\Auth;

class User 
{
    // Verify User/Guest
    public function __construct($class = 0, $force = 0, $autoclean = false)
    {
        Auth::user($class, $force, $autoclean);
        global $CURUSER;
        return $CURUSER;
    }
    
    // Get User
    public function has($value)
    {
        global $CURUSER;
        if ($CURUSER) {
            return $CURUSER[$value];
        } else {
            return 0;
        }
    }
    
    // Get User
    public function isStaff($owner = 0)
    {
        if ($owner != 0) {
            if ($this->has('id') == $owner) {
                return true;
            }
            return false;
        }
        if ($this->has('class') > 4) {
            return true;
        } else {
            return false;
        }
    }
       
    // Get User
    public function loggedin()
    {
        !empty($this->has('id') && is_int($this->has('id'))) ? true :false;
    }
 
    public function permission($permission, $id = 0)
    {
        global $CURUSER;
        switch ($permission) {
            case 'view_torrents':
                $allow = $CURUSER['view_torrents'] != "yes" && CONFIG['MEMBERSONLY'] ? false : true;
            break;
            case 'forum':
                if (!CONFIG['FORUMS']) {
                    Redirect(Url(), Txt("FORUM_AVAILABLE"));
                }
                if (!CONFIG['FORUMS_GUESTREAD'] && !$this->has('id')) {
                    Redirect(Url(), Txt("NO_PERMISSION"));
                }
                if ($this->has('forumbanned') == "yes" || $this->has('view_forum') == "no") {
                    Redirect(Url(), Txt("FORUM_BANNED"));
                }
            break;
            case 'view_users':
                if ($id == 0) {
                    $allow = $this->has('view_users') != "yes" && $this->has('class') <= _MODERATOR ? false : true;
                }
                $allow = $this->has('view_users') != "yes" && $this->has('id') != $id ? false : true;
            break;
            case 'view_news':
                if ($id == 0) {
                    $allow = $this->has('view_users') != "yes" && $this->has('class') <= _MODERATOR ? false : true;
                }
                $allow = CONFIG['NEWSON'] && $this->has('view_news') == "yes" ? true : false;
            break;
            case 'view_forum':
                if ($id == 0) {
                    $allow = $this->has('view_users') != "yes" && $this->has('class') <= _MODERATOR ? false : true;
                }
                $allow = ($this->has('forumbanned') == "yes" || $this->has('view_forum') == "no") ? true : false;
            break;
            default:
                if ($id == 0) {
                    $allow = $this->has($permission) != "yes"  ? true : false;
                }
                $allow = $this->has($permission) == "yes"  ? true : false;
        }
        return $allow;
    }
    
}