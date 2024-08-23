<?php  block_begin($page_title) ?>
<div class="tt_blockcenter rounded p-3">
    <form method="post" action="<?= Url("moviedatabase") ?>" autocomplete="off">

    <div class="text-center">
       <?= LANG["SEARCH"] ?> TMDB <a href='https://www.themoviedb.org/'>www.themoviedb.org</a>
       <input id="search" type="text" class="form-control fullwidth" name="search" minlength="3" maxlength="40" value='<?php echo $V_search ?>' required autofocus><br>
    </div>

    <div class="text-center">
        <button id="type" name="type" type="submit" class="btn ttbtn" value="movie"><?= LANG["MOVIE"] ?></button>
	    <button id="type" name="type" type="submit" class="btn ttbtn" value="show"><?= LANG["SHOW"] ?></button>
	    <button id="type" name="type" type="submit" class="btn ttbtn" value="person"><?= LANG["PERSON"] ?></button>
	</div>

    </form>
</div><br>

<?php if ($V_link != '') : ?>
<div class="tt_blockcenter rounded">
<div class="text-center">
    <a><h3><?= LANG["SEARCH"] ?> <?= $V_type ?></h3></a>
        <?= $V_link ?>
</div>
</div>
<?php endif; ?>
<?php block_end(); ?>