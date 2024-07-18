<?php block_begin($V_title) ?>
<p id="shoutboxstaff"></p>
<form id='contactForm1' name='contactForm1' action='<?= Url('adminshoutbox/add') ?>' method='post'>
    <div class="row  rounded p-2">
        <div class="col-md-12">
            <?php echo shoutbbcode("contactForm1", "message"); ?>
        </div>
    </div>
</form>
<?php block_end() ?>