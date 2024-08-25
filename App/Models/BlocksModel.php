<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Cache;

/*
* Blocks Table Data
*/
class BlocksModel
{
    // Admin Insert Block
    public function insert($updateset)
    {
        $stmt = DB::insert('blocks', $updateset);
        return $stmt;
	}
	
    // Get Position
    public static function getPosition($position)
    {
        $getposition = DB::fetchColumn('blocks', 'COUNT(position)', ['position =', 'enabled ='], [$position,1]);
        $newposition = $getposition + 1;
        return $newposition;
    }

    // Update Blocks
    public static function resort($place)
    {
        $sortleft = DB::fetchAll('blocks', 'sort, id', ['position =', 'enabled ='], [$place,1], 'ORDER BY sort ASC');
        $i = 1;
        foreach ($sortleft as $sort) {
            DB::update('blocks', ['sort'], ['id='], [$i, $sort["id"]]);
            $i++;
        }
    }

    // Admin Refresh Page
    public function editable()
    {
        // Prune Block Cache.
        $TTCache = new Cache();
        $TTCache->Delete("sql_cache/blocks_left");
        $TTCache->Delete("sql_cache/blocks_middle");
        $TTCache->Delete("sql_cache/blocks_right");

        $nextleft = self::getPosition('left');
        $nextmiddle = self::getPosition('middle');
        $nextright = self::getPosition('right');

        // Delete
        if ($_POST["delete"]) {
            $ids = $_POST['ids'];
            $sort_ids = array_map("intval", $ids);
            $sort_ids = implode(",", $sort_ids);
            DB::deleteByIds('blocks', 'id', $sort_ids);

            // Move Blocks
            self::resort('left');
            self::resort('middle');
            self::resort('right');
        }

        // Move to left
        if (Id($_GET["left"])) {
            DB::update('blocks', ['position','sort'], ['id='], ['left', $nextleft, $_GET["left"]]);
            self::resort('middle');
            self::resort('right');
        }

        // Move to center
        if (Id($_GET["middle"])) {
            DB::update('blocks', ['position','sort'], ['id='], ['middle', $nextmiddle, $_GET["middle"]]);
            self::resort('left');
            self::resort('right');
        }

        // Move to right
        if (Id($_GET["right"])) {
            DB::update('blocks', ['position','sort'], ['id='], ['right', $nextright, $_GET["right"]]);
            self::resort('left');
            self::resort('middle');
        }

        // Move upper
        if (Id($_GET["up"])) {
            $sort = (int) $_GET["sort"];
            DB::update('blocks', ['sort'], ['sort =', 'id !=', 'position ='], [$sort, $sort - 1, $_GET["up"], $_GET["position"]]);
            DB::update('blocks', ['sort'], ['id ='], [($sort - 1), $_GET["up"]]);
        }

        // Move lower
        if (Id($_GET["down"])) {
            $sort = (int) $_GET["sort"];
            DB::update('blocks', ['sort'], ['id ='], [$sort + 1, $_GET["down"]]);
            DB::update('blocks', ['sort'], ['sort =', 'id !=', 'position ='], [$sort, $sort + 1, $_GET["down"], $_GET["position"]]);
        }

        // Update
        $res = DB::fetchAll('blocks', '*', '', 'ORDER BY id');
        if (!$_GET["up"] && !$_GET["down"] && !$_GET["right"] && !$_GET["left"] && !$_GET["middle"]) {
            $update = array();
            foreach ($res as $upd) {
                $id = $upd["id"];
                $update[] = "enabled = " . $_POST["enable_" . $upd["id"]];
                $update[] = "named = '" . $_POST["named_" . $upd["id"]] . "'";
                $update[] = "description = '" . $_POST["description_" . $upd["id"]] . "'";
                if (($upd["enabled"] == 0) && ($upd["position"] == "left") && ($_POST["enable_" . $upd["id"]] == 1)) {
                    $update[] = "sort = " . $nextleft;
                } elseif (($upd["enabled"] == 0) && ($upd["position"] == "middle") && ($_POST["enable_" . $upd["id"]] == 1)) {
                    $update[] = "sort = " . $nextmiddle;
                } elseif (($upd["enabled"] == 0) && ($upd["position"] == "right") && ($_POST["enable_" . $upd["id"]] == 1)) {
                    $update[] = "sort = " . $nextright;
                } elseif (($upd["enabled"] == 1) && ($upd["position"] == "left") && ($_POST["enable_" . $upd["id"]] == 0)) {
                    $update[] = "sort = 0";
                } elseif (($upd["enabled"] == 1) && ($upd["position"] == "middle") && ($_POST["enable_" . $upd["id"]] == 0)) {
                    $update[] = "sort = 0";
                } elseif (($upd["enabled"] == 1) && ($upd["position"] == "right") && ($_POST["enable_" . $upd["id"]] == 0)) {
                    $update[] = "sort = 0";
                } else {
                    $update[] = "sort = " . $upd["sort"];
                }
                DB::run("UPDATE blocks SET " . implode(", ", $update) . " WHERE id=$id");
            }
        }

        // Set Block
        self::resort('left');
        self::resort('middle');
        self::resort('right');
    }

    // Admin Get All
    public function getAll()
    {
        $res = DB::newSelect('blocks', '*', '', 'ORDER BY enabled DESC, position, sort')->fetchAll();
        foreach ($res as $fileexist) {
            $files[] = $fileexist["name"] . "_block.php";
        }
        $data = [
            'res' => $res,
            'files' => $files
        ];

        return $data;
    }
}