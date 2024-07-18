<?php

use App\Models\StaffMessagesModel;
use App\Models\TasksModel;
use App\Models\UsersModel;

//Get Last Cleanup
$row = TasksModel::fetchColumn('last_time', ['task='], ['cleanup']);
if (!$row) {
    $lastclean = "never done...";
} else {
    $lastclean = get_elapsed_time($row);
}
$row = TasksModel::version();
$mysqlver = $row['version'];
$newstaffmessage = StaffMessagesModel::fetchColumn('count(id)', ['answered='], [0]);
$pending = UsersModel::fetchColumn('count(id)', ['status=', 'invited_by='], ['pending',0])
?><br>
<div class="tt_block rounded">
    <div class="text-center">
        <?= Txt("LAST_CLEANUP_PERFORMED") ?>: <?= $lastclean ?> ago [<a href='<?= Url('admintask/cleanup') ?>'><b><?= Txt("FORCE_CLEAN") ?></b></a>]<br>
        <?php
        function apache_version()
        {
            $ver = explode(" ", $_SERVER["SERVER_SOFTWARE"], 3);
            return ($ver[0] . " " . $ver[1]);
        }
        ?>
        <b><?= Txt("STAFF_MESSAGES") ?>:</b> <a href='<?= Url('admincontact') ?>'><b>(<?= $newstaffmessage ?>)</b></a><br>
        <b><?= Txt("USERS_AWAITING_VALIDATION") ?>:</b> <a href='<?= Url('adminconfirmreg') ?>'><b>(<?= $pending ?>)</b></a><br>
        <?= Txt("VERSION_MYSQL") ?>: <b><?= $mysqlver ?></b>&nbsp;-&nbsp;
        <?= Txt("VERSION_PHP") ?>: <b><?= phpversion() ?></b>&nbsp;-&nbsp;
        <?= Txt("VERSION_APACHE") ?>: <b><?= apache_version() ?></b><br>
        <a href="<?= Url('admintask/cache') ?>"><b><?= Txt("PURGE_CACHE") ?></b></a><br>
    </div>
</div>