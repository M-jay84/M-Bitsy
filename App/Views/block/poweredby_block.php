<?php
if ($USER->has('id')) {
    block_begin("Powered By");
    ?>
    <center>
    <a href="https://getbootstrap.com/" target="_blank"><img
      src="<?= Url('assets/images/misc/boot.webp') ?>" alt="Bootstrap" title="Bootstrap" height="40" width="40" /></a>
    <a href="https://phpdelusions.net/pdo" target="_blank"><img
      src="<?= Url('assets/images/misc/pdo.webp') ?>" alt="PDO" title="PDO" height="40" width="40" /></a>
    <a href="https://www.php.net/" target="_blank"><img
      src="<?= Url('assets/images/misc/php.webp') ?>" alt="PHP" title="PHP" height="40" width="40" /></a>
    </center>
    <?php
    block_end();
}