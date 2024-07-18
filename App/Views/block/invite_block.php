<?php
if ((CONFIG['INVITEONLY'] || CONFIG['ENABLEINVITES']) && $USER->has('id')) {
   $invites = $USER->has('invites');
   $message = sprintf(Txt('YOU_HAVE_INVITES'), $invites);
   block_begin(Txt("INVITES")); ?>
   <div class="text-center">
	<?= $message ?>
   <?php
   if ($invites > 0) {  ?>
      <a href="<?= Url('invite') ?>" class="btn ttbtn"><?= Txt("SEND") ?></a> <?php
   }
   if ($USER->has('invitees') > 0) { ?>
      <a href="<?= Url("invite/invitetree/User(id]") ?>" class="btn ttbtn"><?= Txt("INVITED") ?></a> <?php
   } ?>
   </div>
	<?php
   block_end();
}