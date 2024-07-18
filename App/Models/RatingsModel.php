<?php

namespace App\Models;

use App\Libs\DB;

/*
* Ratings Table Data
*/
class RatingsModel
{
    // Show Rating
    public static function ratingTor($id, $rating = 0)
    {
        if (!$rating) {
            $rating = "<b>" . Txt("RATINGS") . ":</b> <b>Not Yet Rated</b><br>";
        } else {
            $average = ceil($rating);
            $rating = "<img src=\"" . URLROOT . "/assets/images/rating/$average.webp\" alt=\"rating: $average/5\" title=\"rating: $average/5\" /></a><br>";
        }
        if ($_SESSION['id']) {
            $ratings = [
                5 => Txt("COOL"),
                4 => Txt("PRETTY_GOOD"),
                3 => Txt("DECENT"),
                2 => Txt("PRETTY_BAD"),
                1 => Txt("SUCKS"),
            ];
            $res = DB::fetchRow('ratings', 'rating, added', ['torrent=', 'user='], [$id, $_SESSION['id']]);
            $row = $res;
            if ($row) {
                $rating .= "<i>(" . Txt("YOU_RATED") . " \"" . $row["rating"] . " - " . $ratings[$row["rating"]] . "\")</i>";
            } else {
                $rating .= "<form style=\"display:inline;\" method=\"post\" action=\"" . URLROOT . "/rating/$id\"><input type=\"hidden\" name=\"id\" value=\"$id\" />";
                $rating .= "<select name=\"rating\" class='ttbtn'>";
                $rating .= "<option value=\"0\">(" . Txt("ADD_RATING") . ")</option>";
                foreach ($ratings as $k => $v) {
                    $rating .= "<option value=\"$k\">$k - $v</option>";
                }
                $rating .= "</select>";
                $rating .= "<input type=\"submit\" class='btn btn-sm ttbtn' value=\"" . Txt("VOTE") . "\" />";
                $rating .= "</form>";
            }
        }

        return $rating;
    }

    // Add Rating
    public static function addRating($id)
    {
        // Check User Input
        $rating = Input('rating', 'int');

        if ($rating <= 0 || $rating > 5) {
            Redirect(Url("torrent/$id"), Txt("INVAILD_RATING"));
        }
        
        $res = DB::insert('ratings', ['torrent'=>$id, 'user'=>$_SESSION['id'] , 'rating'=>$rating, 'added'=>get_date_time()]);
        if ($res) {
            if ($res->errorCode() == 1062) {
                Redirect(Url("torrent/$id"), Txt("YOU_ALREADY_RATED_TORRENT"));
            } else {
                Redirect(Url("torrent/$id"), Txt("ACTION_FAILED"));
            }
        }
        
        DB::run("UPDATE torrents SET numratings = numratings + 1, ratingsum = ratingsum + $rating WHERE id = $id");
        Redirect(Url("torrent/$id"), Txt("RATING_THANK"));
    }
    
}