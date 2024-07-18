<?php

namespace App\Models;

use App\Libs\DB;

/*
* Likes Table Data
*/
class LikesModel
{
    // Add/Delete Like
    public static function switch($id, $type, $post = 0)
    {
        switch ($type) {
            
            case 'liketorrent':
                DB::insert('likes', ['user'=>$_SESSION['id'], 'liked'=>$id, 'added'=>get_date_time(), 'type'=>'torrent', 'reaction'=>'like']);
                Redirect(Url("torrent/$id"), Txt("ITEM_LIKED"));
                break;

            case 'unliketorrent':
                DB::delete('likes', ['user' =>$_SESSION['id'], 'liked' => $id, 'type'=>'torrent']);
                Redirect(Url("torrent/$id"), Txt("ITEM_UNLIKED"));
                break;

            case 'torrent':
                DB::insert('likes', ['user' => $_SESSION['id'], 'liked' => $id, 'added' => get_date_time(), 'type' => 'torrent']);
                Redirect(Url("torrent/$id"), Txt("GIVE_APPRECIATE"));
                break;
    
            case 'thanksforum':
                DB::insert('likes', ['user' => $_SESSION['id'], 'liked' => $id, 'added' => get_date_time(), 'type' => 'forum']);
                Redirect(Url("post/$post"), Txt("GIVE_APPRECIATE"));
                break;
    
            default:
                Redirect(Url(), Txt("ITEM_LIKED"));
                break;
        }
    }
 
    // Fetch Thanks Row
    public static function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('likes', $columns, $where, $values, $other);
        return  $stmt;
    }
 
    // Show Who liked
    public static function showLikes($data)
    {
        if (CONFIG['ALLOWLIKES']) {
            $data1 = DB::fetchRow('likes', 'user', ['liked=', 'type=', 'user=', 'reaction='], [$data['id'],'torrent',$_SESSION['id'],'like']);
            $likes = $data1;
            if ($likes) {
                $show['like'] = "&nbsp;<a href='". URLROOT ."/like/$data[id]/unliketorrent'><i class='fa fa-thumbs-down' title='UnLike'></i></a><br>";
            } else {
                $show['like'] = "&nbsp;<a href='".URLROOT."/like/$data[id]/liketorrent'><i class='fa fa-thumbs-up' title='Like'></i></a><br>";
            }
        }
        if (CONFIG['ALLOWLIKES']) {
            $data3 = DB::fetchAll('likes', 'user', ['liked=', 'type='], [$data['id'], 'torrent']);
            foreach ($data3 as $stmt) :
                $show['liked'] .= "<a href='" . URLROOT . "/profile/$stmt[user]'>" . coloredname($stmt['user']) . "</a>&nbsp;";
            endforeach;
        }
        return $show;
    }

    // Show Who Thanked
    public static function showThanks($target)
    {
        $data3 = DB::run("SELECT * FROM `users` AS u LEFT JOIN `likes` AS l ON(u.id = l.user) WHERE liked=? AND type=?", [$target, 'torrent']);
        $count = $data3->rowCount();
        if ($count > 0) {
            $thanks = 'Thanks by<br>';
            foreach ($data3 as $stmt) :
                $thanks .= "<a href='" . URLROOT . "/profile/$stmt[id]'>" . coloredname($stmt['id']) . "</a>&nbsp;";
            endforeach;
            $thanks .= "<br>";
        } else {
            $thanks = '';
        }
        return $thanks;
    }
}