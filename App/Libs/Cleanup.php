<?php

namespace App\Libs;

use PDO;
use App\Models\LogsModel;
use App\Models\GroupsModel;
use App\Models\WarningsModel;
use App\Libs\DB;
use App\Models\MessagesModel;
use App\Models\SnatchedModel;
use App\Models\TasksModel;
use App\Models\TorrentsModel;
use App\Models\UsersModel;

/*
* Clean Old Data (10 mins)
*/
class Cleanup
{
    // Automatic System Update Function
    public static function autoClean()
    {
        $row = DB::fetchRow('tasks', 'last_time', ['task ='], ['cleanup']);
        if (!$row) {
            TasksModel::insert(['task =','last_time ='], ['cleanup',gmtime()]);
            return;
        }

        if ($row['last_time'] + CONFIG['AUTOCLEANINTERVAL'] > gmtime()) {
            return;
        }

        $planned_clean = TasksModel::update(['last_time'], ['task=', 'last_time ='], [gmtime(), 'cleanup', $row['last_time']]);
        if (!$planned_clean) {
            return;
        }

        self::run();
    }

    // Run Cleanup Operations
    public static function run()
    { 
        self::deletePeers();
        self::makeInvisible();
        self::bonus();
        self::vipUntil();
        self::pendingUser();
        self::deleteLogs();
        self::freeleech();
        
        if (CONFIG['RATIOWARNENABLE']) {
            self::ratioWarn();
        }
        self::expiredWarn();
        self::isWarned();
        self::autoInvite();
        if (CONFIG['HNR_ON']) {
            self::hitAndRun();
        }
        //self::autoPromote(); todo
    }

    // LOCAL TORRENTS - DELETE OLD NON-ACTIVE PEERS
    public static function deletePeers()
    {
        $deadtime = get_date_time(gmtime() - CONFIG['ANNOUNCEINTERVAL']);
        DB::run("DELETE FROM peers WHERE last_action < ?", [$deadtime]);
    }

    // LOCAL TORRENTS - MAKE NON-ACTIVE/OLD TORRENTS INVISIBLE
    public static function makeInvisible()
    {
        $deadtime = gmtime() - CONFIG['MAXDEADTORRENTTIMEOUT'];
        DB::run("UPDATE torrents SET visible=?
                 WHERE visible=? AND last_action < FROM_UNIXTIME(?) AND seeders = ? AND leechers = ? AND external !=?",
                 ['no', 'yes', $deadtime, 0, 0, 'yes']);
    }

    // Set Bonus every hour
    public static function bonus()
    {
        $row = DB::fetchRow('tasks', 'last_time', ['task='], ['bonus']);
        if (!$row) {
            TasksModel::insert([ 'task'=>'bonus','last_time'=>gmtime()]);
        }
        if ($row['last_time'] + CONFIG['ADDBONUS'] < gmtime()) {
            $res1 = DB::run("SELECT DISTINCT userid as peer, (
                                SELECT DISTINCT COUNT( torrent )
                             FROM peers
                             WHERE seeder = ?  AND userid = peer) 
                             AS count
                             FROM peers WHERE seeder = ?", ['yes', 'yes'])->fetchAll();
            foreach ($res1 as $row) {
                DB::run("UPDATE users SET seedbonus = seedbonus + '" . (CONFIG['BONUSPERTIME'] * $row['count']) . "' WHERE id = ?", [$row['peer']]);
                DB::update(' tasks', ['last_time'], ['task='], [gmtime(), 'bonus']);
            }
        }
    }

    // Reset VIP Until
    public static function vipUntil()
    {
        $rowv1 = UsersModel::fetchAll('id, oldclass', ['vipuntil <', 'oldclass !='], [get_date_time(), 0]);
        foreach ($rowv1 as $rowv) {
            MessagesModel::insert(['sender'=>0, 'receiver'=>$rowv['id'], 'added'=>get_date_time(), 'subject'=> 'Your VIP class stay has just expired', 'msg'=>'Your VIP class stay has just expired']);
            UsersModel::update(['class','oldclass','vipuntil'], 'vipuntil <', [$rowv['oldclass'], 0, null, get_date_time()]);
        }
    }

    // DELETE PENDING USER ACCOUNTS OVER TIMOUT AGE
    public static function pendingUser()
    {
        $deadtime = gmtime() - CONFIG['SIGNUPTIMEOUT'];
        DB::run("DELETE FROM users WHERE status = ? AND added < FROM_UNIXTIME(?)", ['pending', $deadtime]);
    }

    // Delete Old Logs
    public static function deleteLogs()
    {
        $ts = gmtime() - CONFIG['LOGCLEAN']  * 86400;
        DB::run("DELETE FROM log WHERE added < FROM_UNIXTIME(?)", [$ts]);
    }

    // Set Free Leech
    public static function freeleech()
    {
        if (CONFIG['FREELEECHGBON']);{
            $query = TorrentsModel::fetchAll('id,name', ['banned =', 'freeleech =', 'size >='], ['no', 0, CONFIG['FREELEECHGB']]);
            if ($query) {
                foreach ($query as $row) {
                    TorrentsModel::update(['freeleech'], ['id='], [1, $row['id']]);
                    LogsModel::write("Freeleech added on  <a href='torrent/$row[id]'>$row[name]</a> because it is bigger than " . CONFIG['FREELEECHGB'] . "");
                }
            }
        }
    }

    // LEECH WARN USERS WITH LOW RATIO
    public static function ratioWarn()
    {
        $downloaded = CONFIG['RATIOWARN_MINGIGS'] * 1024 * 1024 * 1024;
        // ADD RATIO WARNING
        $res = UsersModel::fetchAll('id,username', ['class <=', 'warned =', 'enabled=', 'uploaded / downloaded <', 'downloaded >='], [_UPLOADER, 'no', 'yes', CONFIG['RATIOWARNMINRATIO'], $downloaded]);
        if ($res) {
            $reason = "Auto Leech warning has been added You have been warned because of having low ratio. You need to get a " . CONFIG['RATIOWARNMINRATIO'] . " before next " . CONFIG['RATIOWARN_DAYSTOWARN'] . " days or your account may be banned.";
            $expiretime = gmdate("Y-m-d H:i:s", gmtime() + (86400 * CONFIG['RATIOWARN_DAYSTOWARN']));
            $msg = "".Txt('YOUVE_WARNED_BY')." " . $_SESSION['username'] . " - Reason: " . $reason . " - Expiry: " . $expiretime . "";
            foreach ($res as $arr) {
                WarningsModel::insert(['userid'=>$arr['id'], 'reason'=>$reason, 'added'=>get_date_time(),'expiry'=>$expiretime,'warnedby'=>0,'type'=>'Poor Ratio']);
            }
        }
        // REMOVE RATIO WARNING
        $res1 = DB::run("SELECT users.id, users.username FROM users INNER JOIN warnings ON users.id=warnings.userid
                         WHERE type=? AND active = ? AND warned = ?  AND enabled=? AND uploaded / downloaded >= ? AND downloaded >= ?", ['Poor Ratio', 'yes', 'yes', 'yes', CONFIG['RATIOWARNMINRATIO'], $downloaded])->fetchAll();
        if ($res1) {
            $reason = "Auto Leech warning has been removed Your warning of low ratio has been removed. We highly recommend you to keep a your ratio up to not be warned again.\n";
            foreach ($res1 as $arr1) {
                WarningsModel::insert(['id'=>$arr1['id'], 'reason'=>$reason,'warned'=>'no']);
            }
        }
        // BAN RATIO WARNED USERS
        $res = DB::run("SELECT users.id, users.username, UNIX_TIMESTAMP(warnings.expiry) AS expiry FROM users INNER JOIN warnings ON users.id=warnings.userid
                        WHERE type=? AND active = ? AND class = ? AND enabled=? AND warned = ? AND uploaded / downloaded < ? AND downloaded >= ?", ['Poor Ratio', 'yes', 1, 'yes', 'yes', CONFIG['RATIOWARNMINRATIO'], $downloaded])->fetchAll();
        if ($res) {
            foreach ($res as $arr) {
                if (gmtime() - $arr["expiry"] >= 0) {
                    UsersModel::update(['warned','enabled'], ['id='], ['no', 'no', $arr['id']]);
                    LogsModel::write("User <a href='" . URLROOT . "/profile/" . $arr["id"] . "'>" . coloredname($arr["id"]) . "</a> has been banned (Auto Leech warning).");
                }
            }
        }
    }

    // Warning Expired
    public static function expiredWarn()
    {
        // REMOVE EXPIRED WARNINGS
        $res = DB::run("SELECT users.id, users.username, warnings.expiry FROM users INNER JOIN warnings ON users.id=warnings.userid
                    WHERE type != ? AND warned = ?  AND enabled=? AND warnings.active = ? AND warnings.expiry < ?", ['Poor Ratio', 'yes', 'yes', 'yes', get_date_time()])->fetchAll();
        if ($res) {
            foreach ($res as $arr1) {
                UsersModel::update(['warned'], ['id='], ['no', $arr1['id']]);
                DB::run("UPDATE warnings SET active = ? WHERE userid = ? AND expiry < ?", ['no', $arr1['id'], get_date_time()]);
                LogsModel::write("Removed warning for $arr1[username]. Expiry: $arr1[expiry]");
            }
        }
    }

    // Set User Warned
    public static function isWarned()
    {
        // UPDATE USERS THAT STILL HAVE ACTIVE WARNINGS
        DB::run("UPDATE users SET warned = 'yes' WHERE warned = 'no' AND id IN (SELECT userid FROM warnings WHERE active = 'yes')");
    }

    // Invite update function
    public static function autoInvites($interval, $minlimit, $maxlimit, $minratio, $invites, $maxinvites)
    {
        $time = gmtime() - ($interval * 86400);
        $minlimit = $minlimit * 1024 * 1024 * 1024;
        $maxlimit = $maxlimit * 1024 * 1024 * 1024;
        $res = UsersModel::fetchAll('id,username,class,invites',
                         ['enabled=', 'status =', 'downloaded >=', 'downloaded <', 'uploaded / downloaded >=', 'warned =', "UNIX_TIMESTAMP(invitedate) <="],
                        ['yes', 'confirmed', $minlimit, $maxlimit, $minratio, 'no', $time]);
        if ($res) {
            foreach ($res as $arr) {
                $maxninvites = $maxinvites[$arr['class']];
                if ($arr['invites'] >= $maxninvites) {
                    continue;
                }
                if (($maxninvites - $arr['invites']) < $invites) {
                    $invites = $maxninvites - $arr['invites'];
                }
                DB::run("UPDATE users SET invites = invites+$invites, invitedate = NOW() WHERE id=$arr[id]");
                LogsModel::write("Gave $invites invites to '$arr[username]' - Class: " . GroupsModel::GetUserClassName($arr['class']) . "");
            }
        }
    }

    // GIVE INVITES ACCORDING TO RATIO/GIGS (max 20)
    public static function autoInvite()
    {
        self::autoInvites(14, 1, 4, 0.90, 2, 20);
    }

    // Set Hit & Runs
    public static function hitAndRun()
    {
        $timenow = gmtime();
        $length = CONFIG['HNR_DEADLINE'] * 86400; // 7 days
        $seedtime = CONFIG['HNR_SEEDTIME'] * 3600; // 48 hours
        // Update Snatched
        DB::run("UPDATE snatched SET hnr = ? WHERE completed = ? AND hnr = ? AND upload < download AND $timenow - $length > start_time AND $seedtime > last_time AND done=?", ['yes', 'yes', 'no', 'no']);
        DB::run("UPDATE `snatched` SET `hnr` = ? WHERE `hnr` = ? AND upload >= download", ['no', 'yes']);
        DB::run("UPDATE `snatched` SET `hnr` = ? WHERE `hnr` = ? AND last_time >= ?", ['no', 'yes', $seedtime]);
        // Do We Have Snatched
        $a = SnatchedModel::fetchAll('DISTINCT uid', ['hnr =', 'done='], ['yes', 'no']);
        if ($a):
            foreach ($a as $b):
                
                // Check User
                $user = $b['uid'];
                $count = SnatchedModel::fetchColumn('count(hnr)', ['uid =', 'hnr ='], [$user, 'yes']);
                $expiretime = gmdate("Y-m-d H:i:s", $timenow + $length);
                // Check Wrning
                $f = WarningsModel::fetchRow('type, active', ['userid ='], [$user]);
                $type = $f['type'];
                // Warn
                if ($count >= CONFIG['HNR_WARN'] && $type != "HnR"):
                    $reason = "" . Txt("CLEANUP_WARNING_FOR_ACCUMULATING") . " " . CONFIG['HNR_WARN'] . " H&R.";
                    $subject = "" . Txt("CLEANUP_WARNING_FOR_H&R") . "";
                    $msg = "" . Txt("CLEANUP_YOU_HAVE_BEEN_WARNEWD_ACCUMULATED") . " " . CONFIG['HNR_WARN'] . " " . Txt("CLEANUP_H&R_INVITE_CHECK_RULE") . "\n[color=red]" . Txt("CLEANUP_MSG_WARNING_7_DAYS_BANNED") . "[/color]";
                    $rov = UsersModel::fetchRow('enabled', ['id ='], [$user]);

                    if ($rov["enabled"] == "yes"):
                        DB::update(' users', ['warned'], ['id='], ['yes', $user]);
                        WarningsModel::delete(['userid'=>$user, 'reason'=>$reason, 'added'=>get_date_time(),'expiry'=>$expiretime,'warnedby'=>0,'type'=>'HnR']);
                        MessagesModel::insert(['sender'=>0, 'receiver'=>$user, 'added'=>get_date_time(),'msg'=>$msg,'subject'=>$subject]);
                    endif;
                endif;
                // Unwarn
                if ($count < CONFIG['HNR_WARN'] && $type == "HnR"):
                    $subject = "" . Txt("CLEANUP_REMOVAL_OF_H&R_WARNING") . "";
                    $msg = "" . Txt("CLEANUP_YOU_NOW_HAVE_LESS_THAN") . " " . CONFIG['HNR_WARN'] . " H&R.\n" . Txt("CLEANUP_YOUR_WARNING_FOR_H&R_HAS_REMOVED") . "";
                    DB::update(' users', ['warned'], ['id='], ['no', $user]);
                    WarningsModel::delete(['userid' =>$user, 'type' => 'HnR']);
                    MessagesModel::insert(['sender'=>0, 'receiver'=>$user, 'added'=>get_date_time(),'msg'=>$msg,'subject'=>$subject]);
                endif;
                // Ban
                if ($count >= CONFIG['HNR_BAN']):
                    $h = UsersModel::fetchRow('username, email, modcomment', ['id ='], [$user]);
                    $modcomment = $h[2];
                    $modcomment = gmdate("d/m/Y") . " - " . Txt("CLEANUP_BANNED_FOR") . " " . $count . " H&R.\n " . $modcomment;
                    DB::update(' users', ['enabled','warned','modcomment'], ['id='], ['no', 'no', $modcomment, $user]);
                    WarningsModel::delete(['userid' =>$user, 'type' => 'HnR']);
                    LogsModel::write(Txt("CLEANUP_THE_MEMBER") . " <a href='account-details.php?id=" . $user . "'>" . $h[0] . "</a> " . Txt("CLEANUP_HAS_BEEN_BANNED_REASON") . " " . $count . " H&R.");
                    $sitename = URLROOT;
                    $body = file_get_contents(EMAILDIR . "/hitnrunban.php");
                    $body = str_replace("%count%", $count, $body);
                    $body = str_replace("%sitename%", $sitename, $body);
                    $TTMail = new TTMail();
                    $TTMail->Send($h[1], Txt("CLEANUP_HAS_BEEN_DISABLED"), "$body", "From: " . CONFIG['SITEEMAIL'] . "", "-f" . CONFIG['SITEEMAIL'] . "");
                endif;
                // Download Ban
                if ($count >= CONFIG['HNR_STOP_DL']){
                    DB::update(' users', ['downloadbanned'], ['id=', 'downloadbanned='], ['yes', $user, 'no']);
                    MessagesModel::insert(['sender'=>0, 'receiver'=>$user, 'added'=>get_date_time(),'msg'=>'Too many Hit & Runs. You are Download Banned','subject'=>'Download Banned']);
                }
            endforeach;
        endif;
    }
    
    // Set Auto Promotion / Demotion
    public static function autoPromote() {
        $minratio = 0.9; # ratio for demotion to LEECHE
        $gigs = 50 * 1073741824; # 50 GB
        $delay2 = get_date_time(gmtime() - 1 * 1); # Joined > 1 month
        
        // auto promote by gb
        $res = UsersModel::fetchAll(['id', 'username'], ['class =', 'uploaded / downloaded >'], [1, $minratio]);
        foreach ($res as $arr) {
            $res_classname = GroupsModel::fetchRow('level', ['group_id ='], [_POWERUSER], 'LIMIT 1');
            if ($res_classname->rowCount() == 1) {
                $arr_classname = $res_classname;
                $new_classname = "$arr_classname[level]";
            }
            $username = $arr['username'];
            DB::update(' users', ['class'], ['id='], [_POWERUSER, $arr['id']]);
            $msgg = '[b]Congratulations[/b], you were automatically promoted to [b]Member[/b] class. Please note that if your ratio drops below [b]" . $minratio . "[/b] at any time,  you will be demoted to [b]Leecher[/b]';
            MessagesModel::insert(['sender'=>0, 'receiver'=>$arr['id'], 'added'=>get_date_time(),'msg'=>$msgg,'subject'=>"You have been promoted as " . $new_classname . ""]);
            unset($res, $arr, $res_classname, $arr_classname,$new_classname);
        }

        // auto demote to leecher
        $res = UsersModel::fetchAll(['id','username','modcomment'], ['class <', 'uploaded / downloaded <'], [4, $minratio]);
        foreach ($res as $arr) {
            $username = $arr['username'];
            $modcomment = $arr['modcomment'];
            $modcomment2 = gmdate("d-M-Y") . " - Has been demoted by System to Leecher \n";
            $modcomment = $modcomment2 . "" . $modcomment;
            $modcom = $modcomment;
            DB::run("UPDATE users SET class = 1, modcomment = CONCAT($modcom,modcomment) WHERE id = $arr[id]");
            $msgg = 'You were automatically demoted to [b]Leecher[/b]. That happened because your ratio dropped below [b]" . $minratio . "[/b]';
            MessagesModel::insert(['sender'=>0, 'receiver'=>$arr['id'], 'added'=>get_date_time(),'msg'=>$msgg,'subject'=> 'You have been demoted to Leecher']);
            LogsModel::write("<a href=/account-details.php?id=$arr[id]><b>$username</b></a> has been demoted by System to <b>Leecher</b> class");
        }
        
        // auto promote to class 3
        $res = DB::run("SELECT id, username FROM users WHERE (class = 1 || class = 2) AND warned = 'no' AND added < $delay2 AND uploaded >= $gigs AND uploaded >= downloaded");
        while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
            $res_classname = GroupsModel::fetchRow('level', ['group_id ='], [3], 'LIMIT 1');
            if ($res_classname->rowCount() == 1) {
                $arr_classname = $res_classname;
                $new_classname = "$arr_classname[level]";
            }
            $username = $arr['username'];
            DB::update(' users', ['class'], ['id='], [3, $arr['id']]);
            $msgg = '[b]Congratulations[/b], you were automatically promoted to [b]Power User[/b] class. Please note that if your ratio drops below [b]" . $minratio . "[/b] at any time,  you will be demoted to [b]Leecher[/b]';
            MessagesModel::insert(['sender'=>0, 'receiver'=>$arr['owner'], 'added'=>get_date_time(),'msg'=>$msgg,'subject'=> '[b]Congratulations[/b]']);
            unset($res, $arr, $res_classname, $arr_classname,$new_classname);
        }

        // uploader mod test
        $query = DB::run("SELECT torrents.owner, COUNT(*)  AS counta 
                          FROM torrents 
                          INNER JOIN users ON (torrents.owner=users.id) 
                          WHERE users.class < 2  AND users.donated = 0 and torrents.banned = 'no' AND torrents.added > DATE_SUB(NOW(), INTERVAL 15 DAY) 
                          GROUP BY torrents.owner");
        while ($UP = $query->fetch(PDO::FETCH_ASSOC)){
            if ($UP['counta'] > 3){
                DB::update(' users', ['class'], ['id='], [_POWERUSER, $UP['owner']]);
                $subject = 'Automatic Promotion To Uploader Status';
                $msg = 'Hello you did 1 upload, you are promoted to Uploader, Bravo !!! ';
                MessagesModel::insert(['sender'=>0, 'receiver'=>$UP['owner'], 'added'=>get_date_time(), 'subject'=> $subject,'msg'=>$msg, 'unread'=>'yes', 'location'=>'in']);
            }
        }

        // Uploader demote if he did not upload 1 torrent over 2 week
        while ($up = $query->fetch(PDO::FETCH_ASSOC)){
            $query2 = DB::run("SELECT name, added, DATE_SUB(NOW(), INTERVAL 15 DAY) AS date_expiration FROM torrents WHERE owner = $up[id]");
            while ($up2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                if ($up2["added"] > $up2["date_expiration"]){
                    $nbre = 0 + 1;
                } else {
                    $nbre = 0;
                }
            }
        }
        if ($nbre < 1) {
            DB::update(' users', ['class'], ['id='], [2, $up['id']]);
            $subject = 'Automatic Downgrading to Member Status';
            $msg = 'You have not uploaded in the last fortnight so you have been demoted from uploader.';
            MessagesModel::insert(['sender'=>0, 'receiver'=>$up['id'], 'added'=>get_date_time(), 'subject'=> $subject,'msg'=>$msg, 'unread'=>'yes', 'location'=>'in']);
        }
    }

}