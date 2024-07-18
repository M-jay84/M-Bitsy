<?php

use App\Models\SnatchedModel;
if (CONFIG['HNR_ON']) {
    $count = SnatchedModel::fetchColumn('count(hnr)', ['uid=', 'hnr='], [$USER->has('id'), 'yes']);
    $msg = sprintf(Txt('HIT&RUNNOTICE'), coloredname($USER->has('id')), $count, $USER->has('id'));
    if ($count > 0) {
        block_begin(Txt('HIT&RUN'));
        ?>
        <div class="text-center">
            <?= $msg ?>
        </div>
        <?php
        block_end();
    }
}