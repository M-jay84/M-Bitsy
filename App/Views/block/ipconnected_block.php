<?php

if (!$_SESSION['id'] && !CONFIG['MEMBERSONLY'] || $_SESSION['id']) {
    block_begin(LANG["CONNECTION"]);

    block_end();
}