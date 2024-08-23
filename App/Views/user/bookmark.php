<?php block_begin($page_title) ?>
<?php usermenu($current_user->id); ?>
<div class='text-center'>
<?php echo sprintf(LANG['BOOKMARK_MESSAGE'], $V_count) ?>
</div>
<form id='bookmark' action='<?= Url('bookmark/action') ?>' method='post'>
    <?php torrenttable($V_bookmarks, 'category,name,dl,magnet,uploader,tube,tmdb,comments,nfo,size,completed,seeders,leechers,health,external,added,speed,wait,rating,bookmark') ?>
    <div class="text-center">
        <button type="submit" class="btn ttbtn"><?= LANG['SUBMIT'] ?></button>
    </div>
</form>
<?= $V_pager ?>
<?php block_end() ?>