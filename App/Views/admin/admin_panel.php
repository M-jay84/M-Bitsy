<?php

use App\Libs\DB;

//Get Last Cleanup
$row = DB::fetchColumn('tasks', 'last_time', ['task='], ['cleanup']);
if (!$row) {
    $lastclean = "never done...";
} else {
    $lastclean = get_elapsed_time($row);
}
$row = DB::run("SELECT VERSION() AS version")->fetch();
$mysqlver = $row['version'];
$newstaffmessage = DB::fetchColumn('staffmessages', 'count(id)', ['answered='], [0]);
$pending = DB::fetchColumn('users', 'count(id)', ['status=', 'invited_by='], ['pending',0])
?><br>
<div class="tt_block rounded">
    <div class="text-center">
        <?= LANG["LAST_CLEANUP_PERFORMED"] ?>: <?= $lastclean ?> ago [<a href='<?= Url('admintask/cleanup') ?>'><b><?= LANG["FORCE_CLEAN"] ?></b></a>]<br>
        <?php
        function apache_version()
        {
            $ver = explode(" ", $_SERVER["SERVER_SOFTWARE"], 3);
            return ($ver[0] . " " . $ver[1]);
        }
        ?>
        <b><?= LANG["STAFF_MESSAGES"] ?>:</b> <a href='<?= Url('admincontact') ?>'><b>(<?= $newstaffmessage ?>)</b></a><br>
        <b><?= LANG["USERS_AWAITING_VALIDATION"] ?>:</b> <a href='<?= Url('adminconfirmreg') ?>'><b>(<?= $pending ?>)</b></a><br>
        <?= LANG["VERSION_MYSQL"] ?>: <b><?= $mysqlver ?></b>&nbsp;-&nbsp;
        <?= LANG["VERSION_PHP"] ?>: <b><?= phpversion() ?></b>&nbsp;-&nbsp;
        <?= LANG["VERSION_APACHE"] ?>: <b><?= apache_version() ?></b><br>
        <a href="<?= Url('admintask/cache') ?>"><b><?= LANG["PURGE_CACHE"] ?></b></a><br>
    </div>
</div>