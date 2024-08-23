<?php block_begin($page_title) ?>
<p class="text-center">
    <a type="button" class="model_form ttbtn btn-sm" data-bs-toggle="modal" data-bs-target="#groupModal"><?= LANG["ADD"] ?></a>
<p>
<div class='table-responsive p-3'>
    <table class='table table-striped'>
        <thead>
            <tr>
                <th><?= LANG["PERMISSION_ID"] ?></th>
                <th><?= LANG["NAME"] ?></th>
                <th><?= LANG["TORRENTS"] ?><br><?= LANG["VIEW_EDIT_DEL"] ?></th>
                <th><?= LANG["MEMBERS"] ?><br><?= LANG["VIEW_EDIT_DEL"] ?></th>
                <th><?= LANG["NEWS"] ?><br><?= LANG["VIEW_EDIT_DEL"] ?></th>
                <th><?= LANG["FORUM"] ?><br><?= LANG["VIEW_EDIT_DEL"] ?></th>
                <th><?= LANG["UPLOAD"] ?></th>
                <th><?= LANG["DOWNLOAD"] ?></th>
                <th><?= LANG["DEL"] ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($V_permissions as $row) : ?>
                <tr>
                    <td><?= $row["permission_id"] ?></td>
                    <td><?= $row["permission_name"] ?></td>
                    <td><?= $row["view_torrents"] ?>/<?= $row["edit_torrents"] ?>/<?= $row["delete_torrents"] ?></td>
                    <td><?= $row["view_users"] ?>/<?= $row["edit_users"] ?>/<?= $row["delete_users"] ?></td>
                    <td><?= $row["view_news"] ?>/<?= $row["edit_news"] ?>/<?= $row["delete_news"] ?></td>
                    <td><?= $row["view_forum"] ?>/<?= $row["edit_forum"] ?>/<?= $row["delete_forum"] ?></td>
                    <td><?= $row["can_upload"] ?></td>
                    <td><?= $row["can_download"] ?></td>
                    <td>
                        <a href='<?= Url("adminpermission/delete/$row[permission_id]") ?>'><i class="fa fa-trash"></i></a>
                        <a href="javascript:;" class="groupAttr" data-bs-toggle="modal" data-bs-target="#groupModal" 
                           data-permission_id="<?= $row['permission_id'] ?>" 
                           data-permission_name="<?= $row['permission_name'] ?>" 
                           data-view_torrents="<?= $row['view_torrents'] ?>" 
                           data-edit_torrents="<?= $row['edit_torrents'] ?>" 
                           data-delete_torrents="<?= $row['delete_torrents'] ?>" 
                           data-view_users="<?= $row['view_users'] ?>" 
                           data-edit_users="<?= $row['edit_users'] ?>" 
                           data-delete_users="<?= $row['delete_users'] ?>" 
                           data-view_news="<?= $row['view_news'] ?>" 
                           data-edit_news="<?= $row['edit_news'] ?>" 
                           data-delete_news="<?= $row['delete_news'] ?>" 
                           data-view_forum="<?= $row['view_forum'] ?>" 
                           data-edit_forum="<?= $row['edit_forum'] ?>" 
                           data-delete_forum="<?= $row['delete_forum'] ?>" 
                           data-can_upload="<?= $row['can_upload'] ?>" 
                           data-can_download="<?= $row['can_download'] ?>">
                            <i class="fa fa-edit"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $('.groupAttr').click(function() {
        var permission_id = $(this).data('permission_id');
        var permission_name = $(this).data('permission_name');
        var view_torrents = $(this).data('view_torrents');
        var edit_torrents = $(this).data('edit_torrents');
        var delete_torrents = $(this).data('delete_torrents');
        var view_users = $(this).data('view_users');
        var edit_users = $(this).data('edit_users');
        var delete_users = $(this).data('delete_users');
        var view_news = $(this).data('view_news');
        var edit_news = $(this).data('edit_news');
        var delete_news = $(this).data('delete_news');
        var view_forum = $(this).data('view_forum');
        var edit_forum = $(this).data('edit_forum');
        var delete_forum = $(this).data('delete_forum');
        var can_upload = $(this).data('can_upload');
        var can_download = $(this).data('can_download');

        $('#permission_id').val(permission_id);
        $('#permission_name').val(permission_name);
        $('#view_torrents').val(view_torrents);
        $('#edit_torrents').val(edit_torrents);
        $('#delete_torrents').val(delete_torrents);
        $('#view_users').val(view_users);
        $('#edit_users').val(edit_users);
        $('#delete_users').val(delete_users);
        $('#view_news').val(view_news);
        $('#edit_news').val(edit_news);
        $('#delete_news').val(delete_news);
        $('#view_forum').val(view_forum);
        $('#edit_forum').val(edit_forum);
        $('#delete_forum').val(delete_forum);
        $('#can_upload').val(can_upload);
        $('#can_download').val(can_download);
    });
</script>

<!-- Add/Edit Modal -->
<form id="submit" method="post" action="<?= Url('adminpermission/submit') ?>">
    <div class="modal fade" id="groupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-body">
                    <input type='hidden' name='permission_id' id="permission_id" />
                    <?= LANG["NAME"] ?>
                    <input type="text" name="permission_name" id="permission_name" class="form-control fullwidth" /><br>
                    <?= LANG["VIEW_TORRENTS"] ?>
                    <select name="view_torrents" id="view_torrents" class='form-select'>
                        <option name="view_torrents" id="view_torrents" value="yes"><?= LANG["YES"] ?></option>
                        <option name="view_torrents" id="view_torrents" value="no"><?= LANG["NO"] ?></option>
                    </select><br>
                    <?= LANG["EDIT_TORRENTS"] ?>
                    <select name="edit_torrents" id="edit_torrents" class='form-select'>
                        <option name="edit_torrents" id="edit_torrents" value="yes"><?= LANG["YES"] ?></option>
                        <option name="edit_torrents" id="edit_torrents" value="no"><?= LANG["NO"] ?></option>
                    </select><br>
                    <?= LANG["DELETE_TORRENTS"] ?>
                    <select name="delete_torrents" id="delete_torrents" class='form-select'>
                        <option name="delete_torrents" id="delete_torrents" value="yes"><?= LANG["YES"] ?></option>
                        <option name="delete_torrents" id="delete_torrents" value="no"><?= LANG["NO"] ?></option>
                    </select><br>
                    <?= LANG["VIEW_USERS"] ?>
                    <select name="view_users" id="view_users" class='form-select'>
                        <option name="view_users" id="view_users" value="yes"><?= LANG["YES"] ?></option>
                        <option name="view_users" id="view_users" value="no"><?= LANG["NO"] ?></option>
                    </select><br>
                    <?= LANG["EDIT_USERS"] ?>
                    <select name="edit_users" id="edit_users" class='form-select'>
                        <option name="edit_users" id="edit_users" value="yes"><?= LANG["YES"] ?></option>
                        <option name="edit_users" id="edit_users" value="no"><?= LANG["NO"] ?></option>
                    </select><br>
                    <?= LANG["DELETE_USERS"] ?>
                    <select name="delete_users" id="delete_users" class='form-select'>
                        <option name="delete_users" id="delete_users" value="yes"><?= LANG["YES"] ?></option>
                        <option name="delete_users" id="delete_users" value="no"><?= LANG["NO"] ?></option>
                    </select><br>
                    <?= LANG["VIEW_NEWS"] ?>
                    <select name="view_news" id="view_news" class='form-select'>
                        <option name="view_news" id="view_news" value="yes"><?= LANG["YES"] ?></option>
                        <option name="View news:" id="view_news" value="no"><?= LANG["NO"] ?></option>
                    </select><br>
                    <?= LANG["EDIT_NEWS"] ?>
                    <select name="edit_news" id="edit_news" class='form-select'>
                        <option name="edit_news" id="edit_news" value="yes"><?= LANG["YES"]; ?></option>
                        <option name="edit_news" id="edit_news" value="no"><?= LANG["NO"]; ?></option>
                    </select><br>
                    <?= LANG["DELETE_NEWS"] ?>
                    <select name="delete_news" id="delete_news" class='form-select'>
                        <option name="delete_news" id="delete_news" value="yes"><?= LANG["YES"]; ?></option>
                        <option name="delete_news" id="delete_news" value="no"><?= LANG["NO"]; ?></option>
                    </select><br>
                    <?= LANG["VIEW_FORUM"] ?>
                    <select name="view_forum" id="view_forum" class='form-select'>
                        <option name="view_forum" id="view_forum" value="yes"><?= LANG["YES"] ?></option>
                        <option name="view_forum" id="view_forum" value="no"><?= LANG["NO"] ?></option>
                    </select><br>
                    <?= LANG["EDIT_FORUM"] ?>
                    <select name="edit_forum" id="edit_forum" class='form-select'>
                        <option name="edit_forum" id="edit_forum" value="yes"><?= LANG["YES"] ?></option>
                        <option name="edit_forum" id="edit_forum" value="no"><?= LANG["NO"] ?></option>
                    </select><br>
                    <?= LANG["DELETE_FORUM"] ?>
                    <select name="delete_forum" id="delete_forum" class='form-select'>
                        <option name="delete_forum" id="delete_forum" value="yes"><?= LANG["YES"] ?></option>
                        <option name="" id="delete_forum" value="no"><?= LANG["NO"] ?></option>
                    </select><br>
                    <?= LANG["CAN_UPLOAD"] ?>
                    <select name="can_upload" id="can_upload" class='form-select'>
                        <option name="can_upload" id="can_upload" value="yes"><?= LANG["YES"] ?></option>
                        <option name="can_upload" id="can_upload" value="no"><?= LANG["NO"] ?></option>
                    </select><br>
                    <?= LANG["CAN_DOWNLOAD"] ?>
                    <select name="can_download" id="can_download" class='form-select'>
                        <option name="can_download" id="can_download" value="yes"><?= LANG["YES"] ?></option>
                        <option name="can_download" id="can_download" value="no"><?= LANG["NO"] ?></option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                    <button type="submit" class="btn btn-sm ttbtn"><?= LANG["SAVE"] ?></button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php block_end() ?>