<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Validate;

/*
* Ratings Table Data
*/
class RatingsModel
{
    // Show Rating
    public static function ratingTor($id, $userid, $rating = 0)
    {
        if (!$rating) {
            $rating = "<b>" . LANG["RATINGS"] . ":</b> <b>Not Yet Rated</b><br>";
        } else {
            $average = ceil($rating);
            $rating = "<img src=\"" . URLROOT . "/assets/images/rating/$average.webp\" alt=\"rating: $average/5\" title=\"rating: $average/5\" /></a><br>";
        }
        if ($userid) {
            $ratings = [
                5 => LANG["COOL"],
                4 => LANG["PRETTY_GOOD"],
                3 => LANG["DECENT"],
                2 => LANG["PRETTY_BAD"],
                1 => LANG["SUCKS"],
            ];
            $res = DB::fetchRow('ratings', 'rating, added', ['torrent=', 'user='], [$id, $userid]);
            $row = $res;
            if ($row) {
                $rating .= "<i>(" . LANG["YOU_RATED"] . " \"" . $row["rating"] . " - " . $ratings[$row["rating"]] . "\")</i>";
            } else {
                $rating .= "<form style=\"display:inline;\" method=\"post\" action=\"" . URLROOT . "/rating/$id\"><input type=\"hidden\" name=\"id\" value=\"$id\" />";
                $rating .= "<select name=\"rating\" class='ttbtn'>";
                $rating .= "<option value=\"0\">(" . LANG["ADD_RATING"] . ")</option>";
                foreach ($ratings as $k => $v) {
                    $rating .= "<option value=\"$k\">$k - $v</option>";
                }
                $rating .= "</select>";
                $rating .= "<input type=\"submit\" class='btn btn-sm ttbtn' value=\"" . LANG["VOTE"] . "\" />";
                $rating .= "</form>";
            }
        }

        return $rating;
    }

    // Add Rating
    public static function addRating($id, $userid)
    {
        // Check User Input
        $rating = Validate::Input('rating', 'int');

        if ($rating <= 0 || $rating > 5) {
            showMessage(("torrent/$id"), LANG["INVAILD_RATING"]);
        }
        
        $res = DB::insert('ratings', ['torrent'=>$id, 'user'=>$userid , 'rating'=>$rating, 'added'=>CONFIG['TT_DATE']]);
        if ($res) {
            if ($res->errorCode() == 1062) {
                showMessage(("torrent/$id"), LANG["YOU_ALREADY_RATED_TORRENT"]);
            } else {
                showMessage(("torrent/$id"), LANG["ACTION_FAILED"]);
            }
        }
        
        DB::run("UPDATE torrents SET numratings = numratings + 1, ratingsum = ratingsum + $rating WHERE id = $id");
        showMessage(("torrent/$id"), LANG["RATING_THANK"]);
    }
    
}