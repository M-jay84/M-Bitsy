<?php block_begin($page_title) ?>
<div class=" rounded p-2">
  <form id="searchall"  method="post" action="<?= Url('adminusersearch') ?>">
    <div class="form-group row">
      <label for="name" class="col-sm-1 col-form-label"><?= LANG["NAME"] ?></label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="name" name="name" value="<?= $V_inputs['name'] ?>">
      </div>
      <label for="ratio" class="col-sm-1 col-form-label"><?= LANG["RATIO"] ?></label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="ratio" name="ratio" value="<?= $V_inputs['ratio'] ?>">
      </div>
      <label for="member" class="col-sm-1 col-form-label"><?= LANG["STATUS"] ?></label>
      <div class="col-sm-3">
        <select id="member" name="member" class='form-select'>
          <option value="">--</option>
          <option value="confirmed"><?= LANG["CONFIRMED"] ?></option>
          <option value="pending"><?= LANG["PENDING"] ?></option>
        </select>
      </div>
      <label for="email" class="col-sm-1 col-form-label"><?= LANG["EMAIL"] ?></label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="email" name="email" value="<?= $V_inputs['email'] ?>">
      </div>
      <label for="uploaded" class="col-sm-1 col-form-label"><?= LANG["UPLOADED"] ?></label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="uploaded" name="uploaded" value="<?= $V_inputs['uploaded'] ?>">
      </div>
      <label for="account" class="col-sm-1 col-form-label"><?= LANG["ENABLED"] ?></label>
      <div class="col-sm-3">
        <select id="account" name="account" class='form-select'>
          <option value="">--</option>
          <option value="yes"><?= LANG["NO"] ?></option>
          <option value="no"><?= LANG["YES"] ?></option>
        </select>
      </div>
      <label for="ip" class="col-sm-1 col-form-label"><?= LANG["IP"] ?></label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="ip" name="ip" value="<?= $V_inputs['ip'] ?>">
      </div>
      <label for="downloaded" class="col-sm-1 col-form-label"><?= LANG["DOWNLOADED"] ?></label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="downloaded" name="downloaded" value="<?= $V_inputs['downloaded'] ?>">
      </div>
      <label for="class" class="col-sm-1 col-form-label"><?= LANG["CLASS"] ?></label>
      <div class="col-sm-3">
        <select id="class" name="class" class='form-select'>
          <option value=""><?= LANG["ANY"] ?></option>
          <?php foreach ($V_groups as $row) : ?>
            <option value="<?= $row['group_id'] ?>"><?= $row['level'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <label for="warned" class="col-sm-1 col-form-label"><?= LANG["WARNED"] ?></label>
      <div class="col-sm-3">
        <select id="warned" name="warned" class='form-select'>
          <option value="">--</option>
          <option value="yes"><?= LANG["YES"] ?></option>
          <option value="no"><?= LANG["NO"] ?></option>
        </select>
      </div>
    </div><br>
    <p class='text-center'> 
    <a href="<?= Url('adminusersearch') ?>" class="btn ttbtn "><?= LANG["RESET"] ?></a>
    <button type="submit" class="btn ttbtn "><?= LANG['SUBMIT'] ?></button>
    </p>
  </form>
</div><br>

<form id="searchresult" method='post' action='<?= Url('adminusersearch') ?>'>
  <div class='table-responsive '>
    <table class='table table-striped'>
      <thead>
        <tr>
          <th><?= LANG["NAME"] ?></th>
          <th><?= LANG["IP"] ?></th>
          <th><?= LANG["EMAIL"] ?></th>
          <th><?= LANG["JOINED"] ?></th>
          <th><?= LANG["LAST_SEEN"] ?></th>
          <th><?= LANG["STATUS"] ?></th>
          <th><?= LANG["ENABLED"] ?></th>
          <th><?= LANG["RATIO"] ?></th>
          <th><?= LANG["UPLOADED"] ?></th>
          <th><?= LANG["DOWNLOADED"] ?></th>
          <th><?= LANG["CLASS"] ?></th>
          <th><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($V_results as $row) : ?>
          <?php $rowratio = $row["downloaded"] > 0 ? number_format($row["uploaded"] / $row["downloaded"], 1) : "---"; ?>
          <tr>
            <td><b><?= coloredname($row['id']) ?></b></td>
            <td><?=  $row['ip'] ?></td>
            <td><?=  $row['email'] ?></td>
            <td><?= utc_to_tz($row['added']) ?></td>
            <td><?= $row['last_access'] ?></td>
            <td><?= $row['status'] ?></td>
            <td><?= $row['enabled'] ?></td>
            <td><?= get_ratio_color($rowratio) ?></td>
            <td><?= mksize($row['uploaded']) ?></td>
            <td><?= mksize($row['downloaded']) ?></td>
            <td><?= $row['class'] ?></td>
            <td><input type='checkbox' name="ids[]" id="ids" value='<?= $row['id'] ?>'></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div><br>

  <?= $V_pager ?>

  <div class='tt_blockcenter rounded p-1'>
    <div class='text-center'>
      <input type='hidden' name="action" value="1"/>
      <input type='submit' name='enable' class='btn btn-success btn-sm' value="<?= LANG["ENABLE"] ?>">
      <input type='submit' name='disable' class='btn btn-danger btn-sm' value="<?= LANG["DISABLE"] ?>">
      <input type='submit' name='warn' class='btn btn-danger btn-sm' value="<?= LANG["WARN"] ?>">
      <input type='submit' name='unwarn' class='btn btn-success btn-sm' value="<?= LANG["UNWARN"] ?>"><br>
      <?= LANG["MOD_COMMENT"] ?><br>
      <input type='text' name='add_comment' />
      
    </div>
  </div>
</form>
<?php block_end() ?>