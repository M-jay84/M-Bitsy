<p class='tt_blockcenter rounded text-center p-3'>
    <b><?= LANG["RULE"] ?></b><br>
    <b><?= stripslashes(CONFIG['UPLOADRULES']); ?></b>
</p><br>
<div class="tt_blockcenter rounded p-2">
    <form name="upload" enctype="multipart/form-data" action="<?= Url('upload/submit') ?>" method="post">
        <div class="text-center">
            <?= LANG["ANNOUNCE_URL"] ?><br>
            <?php foreach ($announce_urls as $key => $value) : ?>
                <b><?= $value ?></b>
            <?php endforeach; ?>
            <?php if (CONFIG['ALLOWEXTERNAL']) : ?>
                <p><b><?= LANG["THIS_SITE_ACCEPTS_EXTERNAL"] ?></b></p>
            <?php endif; ?>
        </div>
        <dl class="row p-4">
            <dt class="col-sm-2"><?= LANG["TORRENT"]; ?></dt>
            <dd class="col-sm-10"><input class="form-control fullwidth" type="file" name="torrent" value='<?= $_FILES['torrent']['name']; ?>' tooltip="<?= LANG["TORRENT_FILE"]; ?>"></dd>
        </dl>
        <dl class="row p-4">
            <dt class="col-sm-2"><?= LANG["NFO"] ?></dt>
            <dd class="col-sm-10"><input class="form-control fullwidth" type="file" name="nfo" value='<?= $_FILES['nfo']['name']; ?>' placeholder="<?= LANG["NFO"]; ?>"></dd>
        </dl>
        <dl class="row p-4">
            <dt class="col-sm-2"><?= LANG["NAME"] ?></dt>
            <dd class="col-sm-10"><input class="form-control fullwidth" type='text' name='name' placeholder="<?= LANG["TORRENT_NAME"]; ?>"><?= LANG["THIS_WILL_BE_TAKEN_TORRENT"]; ?></dd>
        </dl>
        <?php if (CONFIG['_OMDBswitch']) : ?>
            <dl class="row p-4">
            <dt class="col-sm-2"><?= LANG["TMDB"] ?></dt>
            <dd class="col-sm-10"><input class="form-control fullwidth" type='text' name='tmdb' placeholder="TMDB Link">Example https://www.themoviedb.org/movie/399566-godzilla-vs-kong</dd>
        </dl>
        <?php endif; ?>
        <?php if (CONFIG['_TMDBswitch']) : ?>
            <dl class="row p-4">
            <dt class="col-sm-2"><?= LANG["IMDB"] ?></dt>
            <dd class="col-sm-10"><input class="form-control fullwidth" type='text' name='imdb' placeholder="IMDB Link">Example https://www.imdb.com/title/tt5034838</a></dd>
        </dl>
        <?php endif; ?>
        <?php if (CONFIG['YOU_TUBE']) : ?>
            <dl class="row p-2">
            <dt class="col-sm-2"><?= LANG["YOUTUBE"]; ?></dt>
            <dd class="col-sm-10"><input class="form-control fullwidth" type='text' name='tube' placeholder="Youtube Link">Example https://www.youtube.com/watch?v=aYzVrjB-CWs</dd>
        </dl>
        <?php endif; ?>
        <dl class="row p-2">
            <dt class="col-sm-2"><?= LANG["IMAGE"] ?> 1</dt>
            <dd class="col-sm-10"><input class="form-control fullwidth" type="file" name="image0" value='<?= $_FILES['nfo']['name']; ?>'></dd>
        </dl>
        <dl class="row p-2">
            <dt class="col-sm-2"><?= LANG["IMAGE"] ?> 2</dt>
            <dd class="col-sm-10"><input class="form-control fullwidth" type="file" name="image1"></dd>
        </dl>
        <div class="text-center">
            <?= LANG["MAX_FILE_SIZE"]; ?>: <?= mksize(CONFIG['IMAGEMAXFILESIZE']); ?><br>
            <?= LANG["ACCEPTED_FORMATS"]; ?>: <?php echo implode(", ", array_unique(CONFIG['ALLOWEDIMAGETYPES'])); ?>
        </div>
        <?php $category = "<select name=\"type\" class='form-select fullwidth'>\n<option value=\"0\">" . LANG["CATEGORY"] . "</option>\n";
                foreach ($catagories as $row) {
                    $category .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["parent_cat"]) . ": " . htmlspecialchars($row["name"]) . "</option>\n";
                }
                $category .= "</select>\n"; ?>
        <div class="form p-4">
            <?php echo $category ?>
        </div>
        <div class="form p-4">
            <select class="form-select" id="validationTags" name="tags[]" multiple data-allow-clear="true" data-suggestions-threshold="0">
                <option selected disabled hidden value=""><?= LANG["CHOSE_TAG"] ?></option>
                <?php foreach ($tags as $row) :
                    echo "<option value=" . $row['name'] . ">" . $row['name'] . "</option>";
                endforeach; ?>
            </select>
        </div>
        <div class="form p-4">
            <?php echo $languages ?>
        </div>
        <?php if ($current_user->class > CONFIG['_VIP']) : ?>
            <div class="form p-4">
                <input class="form-check-input" type='checkbox' name='free' value=1><span></span>
                <?= LANG["CHECK_FREELEECH"] ?>
            </div>
        <?php endif; ?>
        <?php if ($current_user->class > CONFIG['_VIP']) : ?>
            <div class="form p-4">
                <input class="form-check-input" type='checkbox' name='vip' value='yes'><span></span>
                <?= LANG["CHECKCONFIG['_VIP']"] ?>
            </div>
        <?php endif; ?>
        <?php if (CONFIG['ANONYMOUSUPLOAD']) : ?>
            <div class="form p-4">
                <input class="form-check-input" type="checkbox" name="anonycheck" value='yes'><span></span>
                <?= LANG["UPLOAD_ANONY"] ?>
            </div>
        <?php endif; ?>
        <p class="text-center"><?= LANG["DESCRIPTION"]; ?></p>
        <?php print textbbcode("upload", "descr"); ?>
        <p class="text-center">
            <input type="submit" class="btn btn-sm ttbtn" value="<?= LANG['SUBMIT'] ?>" /><br />
        </p>
    </form>
</div>