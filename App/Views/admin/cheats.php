<div class="tt_blockcenter rounded">
    <div class="tt_blockhead text-center">
        <?= $page_title ?>
    </div>
    <div class="p-3">
        <form action='<?= Url('admincheat') ?>' method='post'>
            <input type='number' class="form-control fullwidth" maxlength='4' name='daysago' placeholder="<?= LANG["NO_OF_DAYS"] ?>" /><br>
            <input type='text' class="form-control fullwidth" maxlength='60' name='megabts' placeholder="<?= LANG["MB_UPLOADED"] ?>" /><br>
            <div class="text-center">
                <input type='submit' class='btn btn-sm ttbtn' value='<?= LANG['SUBMIT'] ?>' />
            </div>
        </form>
    </div>
</div><br>

<?php block_begin($page_title) ?>
<div class='text-center'>
    <?= $V_message ?>
</div>
<div class='table-responsive'>
    <table class='table table-striped'>
        <thead>
            <tr>
                <th><?= LANG['USERNAME'] ?></th>
                <th><?= LANG["UPLOADED"] ?></th>
                <th><?= LANG["DOWNLOADED"] ?></th>
                <th><?= LANG["RATIO"] ?></th>
                <th><?= LANG["AVG_UPLOAD"] ?></th>
                <th><?= LANG["PM"] ?></th>
                <th><?= LANG["JOINED"] ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($V_users as $row) : ?>
                <?php
                $joindate = "" . get_elapsed_time(sql_timestamp_to_unix_timestamp($row['added'])) . " ago";
                $seconds = mkprettytime(utc_to_tz_time() - utc_to_tz_time($row['added']));
                $days = explode("d ", $seconds);
                if (sizeof($days) > 1) {
                    $dayUpload = $row['uploaded'] / $days[0];
                    $dayDownload = $row['downloaded'] / $days[0];
                }
                $userratio = $row['downloaded'] > 0 ? number_format($row['uploaded'] / $row['downloaded'], 1) : "---";
                ?>
                <tr>
                    <td><?= coloredname($row['id']) ?></td>
                    <td><?= mksize($row['uploaded']) ?></td>
                    <td><?= mksize($row['downloaded']) ?></td>
                    <td><?= get_ratio_color($userratio) ?></td>
                    <td><?= mksize($dayUpload) ?></td>
                    <td><a href='messages/create?id=<?= $row['id'] ?>'>PM</a></td>
                    <td><?= $joindate ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php block_end() ?>