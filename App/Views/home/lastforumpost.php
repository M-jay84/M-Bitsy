<?php
if (CONFIG['LATESTFORUMPOSTONINDEX']) {
    block_begin(Txt('LATEST_POST'));
    latestforumposts();
    block_end();
}