<?php
if ((CONFIG['INVITEONLY'] || CONFIG['ENABLEINVITES']) && $_SESSION['id']) {
   $invites = $_SESSION['invites'];
   $message = sprintf(LANG['YOU_HAVE_INVITES'], $invites);
   block_begin(LANG["INVITES"]); ?>
   <div class="text-center">
	<?= $message ?>
   <?php
   if ($invites > 0) {  ?>
      <a href="<?= Url('invite') ?>" class="btn ttbtn"><?= LANG["INVITE"] ?></a> <?php
   }
   if ($_SESSION['invitees'] > 0) { ?>
      <a href="<?= Url("invite/invitetree/$_SESSION[id]") ?>" class="btn ttbtn"><?= LANG["INVITED"] ?></a> <?php
   } ?>
   </div>
	<?php
   block_end();
}