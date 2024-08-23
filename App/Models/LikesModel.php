<?php

namespace App\Models;

use App\Libs\DB;

/*
* Likes Table Data
*/
class LikesModel
{
    // Add/Delete Like
    public function switch($id, $type, $data = [], $post = 0)
    {
        switch ($type) {
            
            case 'liketorrent':
                DB::insert('likes', ['user'=>$data['current_user']->id, 'liked'=>$id, 'added'=>get_date_time(), 'type'=>'torrent', 'reaction'=>'like']);
                showMessage(Url("torrent/$id"), LANG["ITEM_LIKED"]);
                break;

            case 'unliketorrent':
                DB::delete('likes', ['user' =>$data['current_user']->id, 'liked' => $id, 'type'=>'torrent']);
                showMessage(Url("torrent/$id"), LANG["ITEM_UNLIKED"]);
                break;

            case 'torrent':
                DB::insert('likes', ['user' => $data['current_user']->id, 'liked' => $id, 'added' => get_date_time(), 'type' => 'torrent']);
                showMessage(Url("torrent/$id"), LANG["GIVE_APPRECIATE"]);
                break;
    
            case 'thanksforum':
                DB::insert('likes', ['user' => $data['current_user']->id, 'liked' => $id, 'added' => get_date_time(), 'type' => 'forum']);
                showMessage(Url("post/$post"), LANG["GIVE_APPRECIATE"]);
                break;
    
            default:
                showMessage(Url(), LANG["ITEM_LIKED"]);
                break;
        }
    }
 
    // Fetch Thanks Row
    public function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('likes', $columns, $where, $values, $other);
        return  $stmt;
    }
 
    // Show Who liked
    public function showLikes($data)
    {
        if (CONFIG['ALLOWLIKES']) {
            $data1 = DB::fetchRow('likes', 'user', ['liked=', 'type=', 'user=', 'reaction='], [$data['id'],'torrent',$data['current_user']->id,'like']);
            $likes = $data1;
            if ($likes) {
                $show['like'] = "&nbsp;<a href='". CONFIG['URLROOT'] ."/like/$data[id]/unliketorrent'><i class='fa fa-thumbs-down' title='UnLike'></i></a><br>";
            } else {
                $show['like'] = "&nbsp;<a href='".CONFIG['URLROOT']."/like/$data[id]/liketorrent'><i class='fa fa-thumbs-up' title='Like'></i></a><br>";
            }
        }
        if (CONFIG['ALLOWLIKES']) {
            $data3 = DB::fetchAll('likes', 'user', ['liked=', 'type='], [$data['id'], 'torrent']);
            foreach ($data3 as $stmt) :
                $show['liked'] .= "<a href='" . CONFIG['URLROOT'] . "/profile/$stmt[user]'>" . coloredname($stmt['user']) . "</a>&nbsp;";
            endforeach;
        }
        return $show;
    }

    // Show Who Thanked
    public function showThanks($target)
    {
        $data3 = DB::run("SELECT * FROM `users` AS u LEFT JOIN `likes` AS l ON(u.id = l.user) WHERE liked=? AND type=?", [$target, 'torrent']);
        $count = $data3->rowCount();
        if ($count > 0) {
            $thanks = 'Thanks by<br>';
            foreach ($data3 as $stmt) :
                $thanks .= "<a href='" . CONFIG['URLROOT'] . "/profile/$stmt[id]'>" . coloredname($stmt['id']) . "</a>&nbsp;";
            endforeach;
            $thanks .= "<br>";
        } else {
            $thanks = '';
        }
        return $thanks;
    }
}