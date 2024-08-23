<?php
if (CONFIG['LATESTFORUMPOSTONINDEX']) {
    block_begin(LANG['LATEST_POST']);
    latestforumposts();
    block_end();
}