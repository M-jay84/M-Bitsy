<?php  block_begin($V_title) ?>
<div class="tt_blockcenter rounded p-2">
    <form method="post" action="<?= Url("moviedatabase$V_search") ?>" autocomplete="off">

    <div class="text-center">
       <?= Txt("SEARCH") ?> TMDB <a href='https://www.themoviedb.org/'>www.themoviedb.org</a><br>
       <label for="search" class="col-form-label"><?= Txt("TMDB_SEARCH") ?></label>
       <input id="search" type="text" class="form-control fullwidth" name="search" minlength="3" maxlength="40" value='<?php echo $V_search ?>' required autofocus><br>
    </div>

    <div class="text-center">
        <button id="type" name="type" type="submit" class="btn ttbtn" value="movie"><?= Txt("SEARCH_MOVIE") ?></button>
	    <button id="type" name="type" type="submit" class="btn ttbtn" value="show"><?= Txt("SEARCH_SHOW") ?></button>
	    <button id="type" name="type" type="submit" class="btn ttbtn" value="person"><?= Txt("SEARCH_PERSON") ?></button>
	</div>

    </form>
</div><br>

<div class="row  rounded">
    <p class='text-center'><legend><b><?php echo $V_title ?></b></legend></p>  
    <div class="col-12 col-md-2"> 
        <?php echo $V_poster ?>
    </div>
    <div class="col-12 col-md-6">
        <?php echo $V_details ?>
    </div>
    <div class="col-12 col-md-4">
        <?php echo $V_info ?>
    </div>
</div>
<?php block_end() ?>