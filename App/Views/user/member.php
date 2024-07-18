<?php
use App\Models\CountriesModel;
 block_begin($V_title) ?>
<div class="row tt_blockcenter rounded">
    <h3 class="text-center">Members</h3>
    <form method='get' action='<?= Url('members') ?>'>
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Search" name='search' aria-label="Search">
            <span></span>
            <select name='class' class='form-select'>
                <option value='-'>(<?= Txt("ANY_CLASS") ?>)</option>
                <?php foreach ($V_groups as $row) : ?>
                    <option value='<?= $row['group_id'] ?>'><?= htmlspecialchars($row['level']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <p class="text-center">
            <button type='submit' class='btn ttbtn btn-sm'><?= Txt("SUBMIT"); ?></button>
        </p>
    </form>
    <p class="text-center">
        <a href='<?= Url('members') ?>'><b><?= Txt("ALL") ?></b></a> -
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
                <th><?= Txt("USERNAME") ?></th>
                <th><?= Txt("REGISTERED") ?></th>
                <th><?= Txt("LAST_ACCESS") ?></th>
                <th><?= Txt("CLASS") ?></th>
                <th><?= Txt("COUNTRY") ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($V_users as $row) : ?>
                <?php $country = CountriesModel::fetchRow($row['country'], 1); ?>
                <tr>
                    <td><b><?= coloredname($row['id']) ?></b><?php echo ($row["donated"] > 0 ? "<i class='fa fa-star' aria-hidden='true' style='color:orange' title='Donated'></i>" : "") ?></td>
                    <td><?= utc_to_tz($row["added"]); ?></td>
                    <td><?= utc_to_tz($row["last_access"]) ?></td>
                    <td><?= Txt($row["level"]); ?></td>
                    <td><?= $country ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $V_pager ?>
<?php block_end(); ?>