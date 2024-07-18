<?php

use App\Models\UsersModel;

?>
<div class="table-responsive" style='height:250px;'>
    <table class='table table-striped p-2'>
        <tr>
            <?php foreach ($data['V_shouts'] as $row) : ?>
                <?php
                $ol3 = UsersModel::fetchColumn('avatar', ['id='], [$row["userid"]]);
                if (!empty($ol3)) {
                    $av = "<img src='" . $ol3 . "' alt='my_avatar' width='20' height='20'>";
                } else {
                    $av = "<img src='" . URLROOT . "/assets/images/misc/default_avatar.webp' alt='my_avatar' width='20' height='20'>";
                }
                if ($row['userid'] == 0) {
                    $av = "<img src='" . URLROOT . "/assets/images/misc/default_avatar.webp' alt='default_avatar' width='20' height='20'>";
                }
                ?>
        <tr>
            <td style="width:119px;">
                <small class="pull-left time d-none d-sm-block" style="width:99px;font-size:11px"><i class="fa fa-clock-o"></i>&nbsp;<?php echo date('jS M,  g:ia', utc_to_tz_time($row['date'])); ?></small>
            <td style="width:119px;"><?= $av ?>&nbsp;
                <b><?= coloredname($row["userid"]) ?>:</b>
            </td>&nbsp;
            <td>
                <?php
                echo nl2br(format_comment($row['message']));
                if ($data['USER']->permission('edit_users')) {
                    echo "&nbsp<a href='" . URLROOT . "/shoutbox/delete/" . $row['msgid'] . "'><i class='fa fa-remove' ></i></a>&nbsp";
                    ?>
                    <a href="javascript:;" class="editAttr" data-bs-toggle="modal" data-bs-target="#editModal" data-id="<?= $row['msgid'] ?>" data-text="<?= $row['message'] ?>" data-user="<?= $row['userid'] ?>"><i class="fa fa-pencil"></i></a>
                    <?php
                }
                if (!$data['USER']->permission('edit_users') && $data['USER']->has('username') == $row['user']) {
                    $ts = modify('date', $row['date'], "+1 day");
                    if ($ts > TT_DATE) {
                        ?>
                        <a href="javascript:;" class="editAttr" data-bs-toggle="modal" data-bs-target="#editModal" data-id="<?= $row['msgid'] ?>" data-text="<?= $row['message'] ?>" data-user="<?= $row['userid'] ?>"><i class="fa fa-pencil"></i></a>
                        <?php
                    }
                }
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>
</div>

<?php if ($data['USER']->has('class') > _UPLOADER) : ?>
     <script>
          $('.editAttr').click(function() {
               var id = $(this).data('id');
               var text = $(this).data('text');
               var user = $(this).data('user');
               $('#id').val(id);
               $('#text').val(text);
               $('#user').val(user);
          });
     </script>
    <form name='shoutboxform' action='<?= Url("shoutbox/edit") ?>' method='post'>
          <div class="modal fade" id="editModal">
               <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
                    <div class="modal-content">
                         <div class="modal-body">
                              <input type='hidden' name='id' id="id" />
                              <input type='hidden' name='user' id="user" />
                              <input type="text" class="form-control fullwidth" name="text" id='text'><br>
                         </div>
                         <div class="modal-footer">
                              <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= Txt("CLOSE") ?></button>
                              <button type="submit" class="btn btn-sm ttbtn"><?= Txt("SUBMIT") ?></button>
                         </div>
                    </div>
               </div>
          </div>
     </form>
    
    <?php endif;
