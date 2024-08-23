<?php use App\Models\TorrentsModel; ?>
<?php block_begin($page_title) ?>
<?php foreach ($V_cats as $cat) {
    $orderby = "ORDER BY torrents.sticky ASC, torrents.id DESC"; //Order
    $where = "WHERE banned = 'no' AND category='$cat[id]' AND visible='yes'";
    $limit = "LIMIT 10"; //Limit

    $res = TorrentsModel::getCatSortAll($where, $V_date_time, $orderby, $limit);
    $numtor = $res->rowCount();
    if ($numtor != 0) {
        echo "<b><a href=" . Url("torrent/browse?cat=$cat[id]") . "'>$cat[name]</a></b>";
        torrenttable($res);
        echo "<br />";
    }
} ?>
<?php block_end(); ?>