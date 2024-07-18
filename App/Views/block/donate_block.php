<?php
if ($USER->has('id')) {
    block_begin(Txt("DONATE")); ?>
    <p class="text-center"><?= Txt("THIS_WOULD_BE_DONATION_INFO") ?></p>
    <?php
    block_end();
}