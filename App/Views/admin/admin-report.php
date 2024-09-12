            'reports' => $reports['stmt'],
<?php block_begin($page_title) ?>
<form id='sort' action='<?= Url("adminreport") ?>'>
<p class="text-center">
    <b><?= LANG["TYPE"] ?>:</b>
    <select name="type" onchange="window.location='<?= URLROOT ?>/adminreport?type='+this.options[this.selectedIndex].value+'&amp;completed='+document.forms['sort'].completed.options[document.forms['sort'].completed.selectedIndex].value">
        <option value=""><?= LANG["ALL"] ?></option>
        <option value="user" <?php echo ($_GET['type'] == "user" ? " selected='selected'" : ""); ?>><?= LANG["USERS"] ?></option>
        <option value="torrent" <?php echo ($_GET['type'] == "torrent" ? " selected='selected'" : ""); ?>><?= LANG["TORRENT"] ?></option>
        <option value="comment" <?php echo ($_GET['type'] == "comment" ? " selected='selected'" : ""); ?>><?= LANG["COMMENT"] ?></option>
        <option value="forum" <?php echo ($_GET['type'] == "forum" ? " selected='selected'" : ""); ?>><?= LANG["FORUM"] ?></option>
    </select>
    <b><?= LANG["COMPLETED"] ?>:</b>
    <select name="completed" onchange="window.location='<?= URLROOT ?>/adminreport?completed='+this.options[this.selectedIndex].value+'&amp;type='+document.forms['sort'].type.options[document.forms['sort'].type.selectedIndex].value">
        <option value="0" <?php echo ($_GET['completed'] == 0 ? " selected='selected'" : ""); ?>><?= LANG["NO"] ?></option>
        <option value="1" <?php echo ($_GET['completed'] == 1 ? " selected='selected'" : ""); ?>><?= LANG["YES"] ?></option>
    </select>
</p>
</form>

<form id="submit" method="post" action="<?= Url('adminreport/submit') ?>">
    <div class='table-responsive  p-3'>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th><?= LANG["REPORTED_BY"] ?></th>
                    <th><?= LANG["TYPE"] ?></th>
                    <th><?= LANG["REASON"] ?></th>
                    <th><?= LANG["DEALT_WITH"] ?></th>
                    <th><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
                </tr>
            </thead>
            <tbody> <?php
                foreach ($V_reports as $row) {
                    $dealtwith = $row["dealtby"] > 0 ? coloredname($row['dealtby']) : 'no'; ?>
                    <tr>
                        <td><?= coloredname($row['addedby']) ?></td>
                        <td><?= $row['type'] ?></td>
                        <td><?= htmlspecialchars($row['reason']) ?></td>
                        <td><?= $dealtwith ?></td>
                        <td><input type="checkbox" name="ids[]" value="<?= $row["id"]; ?>" /></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="text-center">
        <?php if ($_GET["completed"] != 1) : ?>
            <input type="submit" class='btn btn-sm ttbtn' name="mark" value="<?= LANG["MARK_COMPLETE"] ?>" />
        <?php endif; ?>
        <input type="submit" class='btn btn-sm ttbtn' name="delete" value="<?= LANG["DEL"] ?>" />
    </div>
</form>

<?= $V_pager ?>
<?php block_end() ?>