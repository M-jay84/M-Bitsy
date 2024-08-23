<?php
use App\Libs\DB;
if (CONFIG['HNR_ON']) {
    $count = DB::fetchColumn('snatched', 'count(hnr)', ['uid=', 'hnr='], [$current_user->id, 'yes']);
    $msg = sprintf(LANG['HIT&RUNNOTICE'], coloredname($current_user->id), $count, $current_user->id);
    if ($count > 0) {
        block_begin(LANG['HIT&RUN']);
        ?>
        <div class="text-center">
            <?= $msg ?>
        </div>
        <?php
        block_end();
    }
}