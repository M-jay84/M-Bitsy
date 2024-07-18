<?php

namespace App\Models;

use App\Libs\DB;
use App\Models\UsersModel;
use App\Models\LogsModel;

/*
*  Teams Table Data
*/
class TeamsModel
{
    // Get User Team
    public static function getTeam($team)
    {
        $res = DB::fetchRow('teams', 'name,image', ['id='], [$team], 'LIMIT 1');
        if ($res) {
            $txt = "<img src='". htmlspecialchars($res["image"]) ."' alt='' />$res[name] <a href='". Url('team') ."'>[View ". Txt("TEAMS") ."]</a>";
        } else {
            $txt = 'n/a';
        };
        return $txt;
    }

    // Delete Team
    public static function delete($id)
    {
         UsersModel::updateWhereIn(['team' => 0], $id, 'team');
        DB::delete('teams', ['id' => $id], 1);
        LogsModel::write($_SESSION['username'] . " has deleted team id:$id");
        Redirect(Url('team'), Txt("ACTION_COMPLETE"));
    }

    // Get Team Members
    public static function getTeams()
    {
        $sql = DB::run("SELECT teams.id, teams.name, teams.image, teams.info, teams.owner, teams.added, users.username, 
                       (SELECT GROUP_CONCAT(id, ' ', username) 
                        FROM users 
                        WHERE FIND_IN_SET(users.team, teams.id) AND users.enabled = 'yes' AND users.status = 'confirmed') AS members 
                        FROM teams 
                        LEFT JOIN users ON teams.owner = users.id 
                        WHERE users.enabled = 'yes' AND users.status = 'confirmed'")->fetchAll();
        return $sql;
    }

    // Teams Dropdown
    public static function dropDownTeams($team)
    {
        $teams = "<option value='0'>--- " . Txt("NOTHING_SELECTED") . " ----</option>\n";
        $sashok = DB::fetchAll('teams', 'id, name', '', 'ORDER BY name');
        foreach ($sashok as $sasha ) {
            $teams .= "<option value='$sasha[id]'" . ($team == $sasha['id'] ? " selected='selected'" : "") . ">$sasha[name]</option>\n";
        }
        return $teams;
    }

    public static function addOrUpdate()
    {
        // Check User Input
        $team_name = Input('name');
        $team_image = Input('image');
        $team_info = Input('info');
        $teamownername = Input('owner');
        $teamid = Input('id');

        // Check Correct Input
        if (!$team_name || !$teamownername || !$team_info) {
            Redirect(Url('team'), Txt("MISSING_FORM_DATA"));
        }

        // Add Team
        if ($teamid) {
            if (!is_numeric($teamownername)) {
                $team_owner = UsersModel::fetchRow('id', ['username='], [$teamownername]);
                $teamownername = $team_owner['id'];
            }
            
            $sql = DB::update(
                'teams', ['name', 'info', 'owner', 'image'], 
                ['id='], [$team_name,$team_info,$teamownername,$team_image,$teamid]
            );
            UsersModel::update(['team'], ['id='], [$teamid,$teamownername]);
            if ($sql) {
                $mss = "<b>Successfully Edited</b>[<a href='" . URLROOT . "/team'>Back</a>]";
                LogsModel::write($_SESSION['username'] . " has edited team ($team_name)");
                Redirect(Url('team'), $mss);
            }
            Redirect(Url('team'), Txt("UPDATE_COMPLETE"));
        } else {
            $ar = UsersModel::fetchRow('id', ['username='], [$teamownername]);
            $team_owner = $ar["id"];
            if (!$team_owner) {
                Redirect(Url('team'), Txt("NO_USER"));
            }
    
            // Add Team
            DB::insert('teams', ['name' => $team_name, 'owner' => $teamownername, 'info' => $team_info, 'image' => $team_image, 'added' => get_date_time()]);
            $tid = DB::lastInsertId();
            UsersModel::update(['team'], ['id='], [$tid,$team_owner]);
            Redirect(Url('team'), Txt("SUCCESS"));
        }
    }
}