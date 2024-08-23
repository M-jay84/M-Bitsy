<?php
if ($_SESSION['id']) {
    block_begin(LANG["DONATE"]); ?>
    <p class="text-center"><?= LANG["THIS_WOULD_BE_DONATION_INFO"] ?></p>
    <?php
    block_end();
}