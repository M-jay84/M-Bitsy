<?php
use App\Models\CountriesModel;
block_begin($TITLE);
 ?>
<div class="row tt_blockcenter rounded">
    <h3 class="text-center"><?= LANG["USERS"] ?></h3>
    <form method='get' action='<?= Url('members') ?>'>
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Search" name='search' aria-label="Search">
            <span></span>
            <select name='class' class='form-select'>
                <option value='-'>(<?= LANG["ANY_CLASS"] ?>)</option>
                <?php foreach ($GROUPS as $row) : ?>
                    <option value='<?= $row['group_id'] ?>'><?= htmlspecialchars($row['level']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <p class="text-center">
            <button type='submit' class='btn ttbtn btn-sm'><?= LANG['SUBMIT']; ?></button>
        </p>
    </form>
    <p class="text-center">
        <a href='<?= Url('members') ?>'><b><?= LANG["ALL"] ?></b></a> -
        <?php
        foreach (range("a", "z") as $l) {
            $L = strtoupper($l);
            $currentletter = $_GET['letter'] ?? '';
            if ($l == $currentletter) {
                print("<b>$L</b>\n");
            } else {
                print("<a href='" . Url("members?letter=$l") . "'><b>$L</b></a>\n");
            }
        }
        ?>
    </p>
</div><br>

<div class='table-responsive  rounded'>
    <table class='table table-striped'>
        <thead>
            <tr>
                <th><?= LANG['USERNAME'] ?></th>
                <th><?= LANG["REGISTERED"] ?></th>
                <th><?= LANG["LAST_ACCESS"] ?></th>
                <th><?= LANG["CLASS"] ?></th>
                <th><?= LANG["COUNTRY"] ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($USERS as $row) : ?>
                <?php $country = CountriesModel::fetchRow($row['country'], 1); ?>
                <tr>
                    <td><b><?= coloredname($row['id']) ?></b><?php echo ($row["donated"] > 0 ? "<i class='fa fa-star' aria-hidden='true' style='color:orange' title='Donated'></i>" : "") ?></td>
                    <td><?= user_timezone_date($row["added"]); ?></td>
                    <td><?= user_timezone_date($row["last_access"]) ?></td>
                    <td><?= $row["level"]; ?></td>
                    <td><?= $country ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $PAGER ?>
<?php block_end(); ?>