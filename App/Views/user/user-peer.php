<?php block_begin($page_title) ?>
<?php usermenu($V_id);
if ($V_user['privacy'] != "strong" || ($current_user->control_panel == "yes") || ($current_user->id == $V_user['id'])) {
    ?>
    <div class='table-responsive p-3'>
    <table class='table table-striped'>
    <thead>
    <tr>
    <th><?= LANG["NAME"] ?></th>
    <th><?= LANG["SIZE"] ?></th>
    <th><?= LANG["UPLOADED"] ?></th>
    <th><?= LANG["DOWNLOADED"] ?></th>
    <th><?= LANG["RATIO"] ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($V_peers as $arr) {
        $userratio = $arr["downloaded"] > 0 ? number_format($arr["uploaded"] / $arr["downloaded"], 1) : "---";
        ?>
        <tr>
        <td><a href='<?= URLROOT ?>/torrent/$arr[torrent]/hit'><b><?= htmlspecialchars($arr["name"]) ?></b></a></td>
        <td><?= mksize($arr["size"]) ?></td>
        <td><?= mksize($arr["uploaded"]) ?></td>
        <td><?= mksize($arr["downloaded"]) ?></td>
        <td><?= get_ratio_color($userratio) ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
    </table>
    </div>
    <?php
}
block_end();