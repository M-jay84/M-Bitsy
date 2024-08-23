<?php block_begin($page_title) ?>
<div class='text-center'>
    <?php echo  sprintf(LANG['WE_HAVE_X_SNATCH'], $V_count); ?>
</div>

<form id="snatched" method="post" action="<?= Url('adminsnatch/submit') ?>">
    <div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><b><?= LANG['NAME'] ?></b></th>
                    <th><b><?= LANG['USER'] ?></b></th>
                    <th><b><?= LANG['UPLOADED'] ?></b></th>
                    <th><b><?= LANG['DOWNLOADED'] ?></b></th>
                    <th><b><?= LANG['SEED_TIME'] ?></b></th>
                    <th><b><?= LANG['STARTED'] ?></b></th>
                    <th><b><?= LANG['LAST_ACTION'] ?></b></th>
                    <th><input type="checkbox" name="checkall" onclick="checkAll(this.form.id)" /></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <?php foreach ($V_snatched as $row) : ?>
                    <td><a href="<?= Url("torrent/$row[tid]") ?>"><?= CutName($row['name'], 35) ?></td>
                    <?php
                    if ($row['username']) {
                        print '<td><b>' . coloredname($row['uid']) . '</b></td>';
                    } else {
                        print '<td>' . $row['ip'] . '</td>';
                    }
                    $stime = mkprettytime($row['last_time']);
                    $startdate = utc_to_tz(get_date_time($row['start_time']));
                    $lastaction = utc_to_tz(get_date_time($row['upload_time']));
                    ?>
                    <td><font color=limegreen><?= mksize($row['upload']) ?></font></td>
                    <td><font color=red><?= mksize($row['download']) ?></font></td>
                    <td><?= (is_null($stime) ? '0' : $stime); ?></td>
                    <td><?= date('d.M.Y H:i', sql_timestamp_to_unix_timestamp($startdate)); ?></td>
                    <td><?= date('d.M.Y H:i', sql_timestamp_to_unix_timestamp($lastaction)); ?></td>
                    <td><input type='checkbox' name='ids[]' value='<?= $row['sid'] ?>'></td>
                <?php endforeach; ?>
                </tr>
            </tbody>
        </table>
    </div><br>
    <div class='text-center'>
       <input type='submit' value='Delete' />
    </div>
</form>
<?= $V_pager ?>
<?php block_end() ?>