<?php block_begin($V_title) ?>
<?= $V_pager ?>

<div class="container justify-content-md-center-2">
    <div class="row">
        <?php foreach ($V_avatars as $row) : ?>
            <div class='col-1'><b><?= coloredname($row['id']) ?></b><br>
            <?php if (!$row['avatar']) : ?>
                <img width="80" src="<?= Url('images/misc/default_avatar.webp') ?>" alt='' /></div>
            <?php else : ?>
                <img width="80" src="<?= htmlspecialchars($row["avatar"]) ?>" alt='' /></div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<?= $V_pager ?>
<?php block_end(); ?>