<?php block_begin(LANG['SHOUTBOX']) ?>
<?php
if (CONFIG['SHOUTBOX'] && !($current_user->hideshoutbox == 'yes')) {
    if ($current_user->id) { ?>
        <p id="shoutbox"></p>
        <form id='contactForm1' name='contactForm1' action='<?= Url('shoutbox/add') ?>' method='post'>
            <div class="row">
                <div class="col-md-12">
                    <?php echo shoutbbcode("contactForm1", "message"); ?>
                </div>
            </div>
        </form> <?php
    } else {
        echo '<p id="shoutbox"></p>';
    }
    block_end();
}