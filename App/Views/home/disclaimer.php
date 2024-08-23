<?php
if (CONFIG['DISCLAIMERON']) {
    block_begin(LANG['DISCLAIMER']);
    echo LANG["DISCLAIMERTXT"];
    block_end();
}