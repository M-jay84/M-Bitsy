<?php block_begin($V_title) ?>
<?php usermenu($USER, $USER->has('id')); ?>
<div class='text-center'>
<?php echo sprintf(Txt('BOOKMARK_MESSAGE'), $V_count) ?>
</div>
<form id='bookmark' action='<?= Url('bookmark/action') ?>' method='post'>
    <?php torrenttable($USER, $V_bookmarks, 'category,name,dl,magnet,uploader,tube,tmdb,comments,nfo,size,completed,seeders,leechers,health,external,added,speed,wait,rating,bookmark') ?>
    <div class="text-center">
        <button type="submit" class="btn ttbtn"><?= Txt("SUBMIT") ?></button>
    </div>
</form>
<?= $V_pager ?>
<?php block_end() ?>