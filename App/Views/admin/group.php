<?php block_begin($V_title) ?>
<p class="text-center">
    <a type="button" class="model_form ttbtn btn-sm" data-bs-toggle="modal" data-bs-target="#groupModal"><?= Txt("ADD") ?></a>
<p>

<div class='table-responsive  p-3'>
    <table class='table table-striped'>
        <thead>
            <tr>
                <th><?= Txt("ID") ?></th>
                <th><?= Txt("NAME") ?></th>
                <th><?= Txt("PERMISSION_ID") ?></th>
                <th><?= Txt("PERMISSION_NAME") ?></th>
                <th><?= Txt("SORT") ?></th>
                <th><?= Txt("DEL") ?></th>
            </tr>
        </thead>
        <tbody> <?php
            foreach ($V_groups as $row) { ?>
                <tr>
                    <td><?= $row["group_id"] ?></td>
                    <td><font color="#<?= $row['Color'] ?>"><?= $row["level"] ?></font></td>
                    <td><a href="<?= Url('adminpermission') ?>"><?= $row["group_role_id"] ?></a></td>
                    </td><td><?= $row["permission_name"] ?></td>
                    </td><td><?= $row["staff_sort"] ?></td>
                    <td>
                        <a href='<?= Url("admingroup/delete/$row[group_id]") ?>'>
                            <i class="fa fa-trash"></i>
                        </a>
                        <a href="javascript:;" class="groupAttr" data-bs-toggle="modal" data-bs-target="#groupModal"
                           data-group_id="<?= $row['group_id'] ?>" 
                           data-group_color="<?= $row['Color'] ?>" 
                           data-level="<?= $row['level'] ?>" 
                           data-group_role_id="<?= $row['group_role_id'] ?>" 
                           data-maxslots="<?= $row['maxslots'] ?>" 
                           data-control_panel="<?= $row['control_panel'] ?>" 
                           data-staff_page="<?= $row['staff_page'] ?>" 
                           data-staff_public="<?= $row['staff_public'] ?>" 
                           data-staff_sort="<?= $row['staff_sort'] ?>">
                            <i class="fa fa-edit"></i>
                        </a>
                    </td>
                </tr> <?php
            } ?>
        </tbody>
    </table>
</div>

<script>
    $('.groupAttr').click(function() {
        var group_id = $(this).data('group_id');
        var group_color = $(this).data('group_color');
        var level = $(this).data('level');
        var group_role_id = $(this).data('group_role_id');
        var maxslots = $(this).data('maxslots');
        var control_panel = $(this).data('control_panel');
        var staff_page = $(this).data('staff_page');
        var staff_public = $(this).data('staff_public');
        var staff_sort = $(this).data('staff_sort');

        $('#group_id').val(group_id);
        $('#group_color').val(group_color);
        $('#level').val(level);
        $('#group_role_id').val(group_role_id);
        $('#maxslots').val(maxslots);
        $('#control_panel').val(control_panel);
        $('#staff_page').val(staff_page);
        $('#staff_public').val(staff_public);
        $('#staff_sort').val(staff_sort);
    });
</script>

<!-- Add/Edit Modal -->
<form id="submit" method="post" action="<?= Url('admingroup') ?>">
    <div class="modal fade" id="groupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-body">
                    <input type='hidden' name='group_id' id="group_id" />
                    <?= Txt("NAME") ?>
                    <input type="text" name="level" id="level" size="10" class='form-control fullwidth'/><br>
                    <?= Txt("COLOUR") ?>
                    <input type="text" name="group_color" id="group_color" class='form-control fullwidth'/><br>
                    <?= Txt("ROLE_ID") ?>
                    <input type="text" name="group_role_id" id="group_role_id" size="10" class='form-control fullwidth'/><br>
                    <?= Txt("SLOTS") ?>
                    <input type="number" name="maxslots" id="maxslots" size="3" class='form-control fullwidth'/><br>
                    <?= Txt("CAN_VIEW_CP") ?>
                    <select name="control_panel" id="control_panel" class='form-select'>
                        <option name="control_panel" id="control_panel"></option>
                        <option name="control_panel" id="control_panel" value="yes"><?= Txt("YES") ?></option>
                        <option name="control_panel" id="control_panel" value="no"><?= Txt("NO") ?></option>
                    </select><br>
                    <?= Txt("STAFF_PAGE") ?>
                    <select name="staff_page" id="staff_page" class='form-select'>
                        <option name="staff_page" id="staff_page"></option>
                        <option name="staff_page" id="staff_page" value="yes"><?= Txt("YES") ?></option>
                        <option name="staff_page" id="staff_page" value="no"><?= Txt("NO") ?></option>
                    </select><br>
                    <?= Txt("STAFF_PUBLIC") ?>
                    <select name="staff_public" id="staff_public" class='form-select'>
                        <option name="staff_public" id="staff_public"></option>
                        <option name="staff_public" id="staff_public" value="yes"><?= Txt("YES") ?></option>
                        <option name="staff_public" id="staff_public" value="no"><?= Txt("NO") ?></option>
                    </select><br>
                    <?= Txt("STAFF_SORT") ?>
                    <input type='text' name='staff_sort' size='3' id='staff_sort' class='form-control fullwidth'/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= Txt("CLOSE") ?></button>
                    <button type="submit" class="btn btn-sm ttbtn"><?= Txt("SUBMIT") ?></button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php block_end() ?>