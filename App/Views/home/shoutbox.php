<?php block_begin(Txt('SHOUTBOX')) ?>
<?php
if (CONFIG['SHOUTBOX'] && !($data['USER']->has('hideshoutbox') == 'yes')) {
    if ($data['USER']->has('id')) { ?>
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