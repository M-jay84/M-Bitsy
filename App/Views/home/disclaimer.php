<?php
if (CONFIG['DISCLAIMERON']) {
    block_begin(Txt('DISCLAIMER'));
    echo Txt("DISCLAIMERTXT");
    block_end();
}