<?php

namespace App\Models;

use App\Libs\DB;

/*
* Likes Table Data
*/
class LikesModel
{
    // Add/Delete Like
    public static function switch($id, $type, $current_user, $post = 0)
    {
        switch ($type) {
            
            case 'liketorrent':
                DB::insert('likes', ['user'=>$current_user->id, 'liked'=>$id, 'added'=>CONFIG['TT_DATE'], 'type'=>'torrent', 'reaction'=>'like']);
                showMessage(("torrent/$id"), LANG["ITEM_LIKED"]);
                break;

            case 'unliketorrent':
                DB::delete('likes', ['user' =>$current_user->id, 'liked' => $id, 'type'=>'torrent']);
                showMessage(("torrent/$id"), LANG["ITEM_UNLIKED"]);
                break;

            case 'torrent':
                DB::insert('likes', ['user' => $current_user->id, 'liked' => $id, 'added' => CONFIG['TT_DATE'], 'type' => 'torrent']);
                showMessage(("torrent/$id"), LANG["GIVE_APPRECIATE"]);
                break;
    
            case 'thanksforum':
                DB::insert('likes', ['user' => $current_user->id, 'liked' => $id, 'added' => CONFIG['TT_DATE'], 'type' => 'forum']);
                showMessage(("post/$post"), LANG["GIVE_APPRECIATE"]);
                break;
    
            default:
                showMessage(URLROOT, LANG["ITEM_LIKED"]);
                break;
        }
    }

    // Show Who liked
    public static function showLikes($data)
    {
        if (CONFIG['ALLOWLIKES']) {
            $data1 = DB::fetchRow('likes', 'user', ['liked=', 'type=', 'user=', 'reaction='], [$data['id'],'torrent',$data['current_user']->id,'like']);
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