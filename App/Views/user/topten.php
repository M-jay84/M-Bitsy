<?php block_begin($page_title); ?>
<div class="row">
    <div class="col-sm-4 m-1 post-message" style='width: 32.33333333%;'>
        <div class='text-center'>
            <?= $V_info['title1'] ?><br>
            <?= toptorrent($V_info['active']); ?>
        </div>
    </div>
    <div class="col-sm-4 m-1 post-message" style='width: 32.33333333%;'>
        <div class='text-center'>
            <?= $V_info['title2'] ?><br>
            <?= toptorrent($V_info['snatch']); ?>
        </div>
    </div>
    <div class="col-sm-4 m-1 post-message" style='width: 32.33333333%;'>
        <div class='text-center'>
            <?= $V_info['title3'] ?><br>
            <?= topcountry($V_info['country']); ?>
        </div>
    </div>
    <div class="col-sm-4 m-1 post-message" style='width: 32.33333333%;'>
        <div class='text-center'>
            <?= $V_info['title4'] ?><br>
            <?= topuser($V_info['uploaders']); ?>
        </div>
    </div>
    <div class="col-sm-4 m-1 post-message" style='width: 32.33333333%;'>
        <div class='text-center'>
            <?= $V_info['title5'] ?><br>
            <?= topuser($V_info['downloaders']); ?>
        </div>
    </div>
    <div class="col-sm-4 m-1 post-message" style='width: 32.33333333%;'>
        <div class='text-center'>
            <?= $V_info['title6'] ?><br>
            <?= topuser($V_info['fastest']); ?>
        </div>
    </div>
    <div class="col-sm-4 m-1 post-message" style='width: 32.33333333%;'>
        <div class='text-center'>
            <?= $V_info['title7'] ?><br>
            <?= topuser($V_info['bestsharer']); ?>
        </div>
    </div>
    <div class="col-sm-4 m-1 post-message" style='width: 32.33333333%;'>
        <div class='text-center'>
            <?= $V_info['title8'] ?><br>
            <?= topuser($V_info['worstshare']); ?>
        </div>
    </div>
</div>
<?php block_end(); ?>