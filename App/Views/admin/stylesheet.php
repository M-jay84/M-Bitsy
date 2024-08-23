<?php block_begin($page_title) ?>
<p class="text-center">
    <a type="button" class="model_form ttbtn btn-sm" data-bs-toggle="modal" data-bs-target="#stylesheetModal"><?= LANG["ADD"] ?></a><br>
<p>
<form id='delete' method='post' action='<?= Url('adminstylesheet/delete') ?>'>
    <div class='table-responsive  p-3'>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th><?= LANG["ID"] ?></th>
                    <th><?= LANG["NAME"] ?></th>
                    <th><?= LANG["THEME_FOLDER_NAME"] ?></th>
                    <th><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($V_styles as $row) : ?>
                <?php
                if (!is_dir("assets/themes/$row[uri]")) {
                    $row['uri'] .= " <b>- " . LANG["THEME_DIR_DONT_EXIST"] . "</b>";
                }
                ?>
                <tr>
                <td><?= $row['id']; ?></td>
                <td><?= $row['name']; ?></td>
                <td><?= $row['uri']; ?></td>
                <td><input name='ids[]' type='checkbox' value='<?= $row['id'] ?>' /></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="text-center">
        <input type='submit' class='btn btn-sm ttbtn' value='<?= LANG["DEL"] ?>' />
    </p>
</form>

<!-- Add Modal -->
<form id="submit" method="post" action="<?= Url('adminstylesheet/submit') ?>">
    <div class="modal fade" id="stylesheetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-body">
                    <?= LANG["THEME_NAME_OF_NEW"] ?><br>
                    <input id="name" type="text" class="form-control fullwidth" name="name"><br>
                    <?= LANG["THEME_FOLDER_NAME_CASE_SENSITIVE"] ?><br>  
                    <input id="uri" type="text" class="form-control fullwidth" name="uri">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                    <button type="submit" class="btn btn-sm ttbtn"><?= LANG['SUBMIT'] ?></button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php block_end() ?>