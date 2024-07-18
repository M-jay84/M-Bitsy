<?php block_begin($V_title) ?>
<div class="text-center">
     Please <a href="<?= Url('staff') ?>">contact</a> a member of staff if you would like a new team creating
</div>
<div class="text-end">
     <i class="fa fa-user-secret m-1 tt_prime" title="<?= Txt('STAFF') ?>"></i>
     <i class="fa fa-plus m-1" data-bs-toggle="modal" data-bs-target="#addModal" title="<?= Txt('ADD') ?>"></i>
</div>

<?php foreach ($V_teams as $row) { ?>
     <div class='table-responsive rounded p-2'>
          <table class='table table-striped'>
               <thead>
                    <tr>
                         <th>
                              <i class="fa fa-star" style="color:<?= $row["image"] ?>" aria-hidden="true"></i>&nbsp;
                              <b><?= htmlspecialchars($row["name"]) ?></b>&nbsp;
                              <?= format_comment($row["info"]) ?>&nbsp;
                              <?= Txt('OWNER'); ?>: <?= coloredname($row["owner"]) ? coloredname($row["owner"]) : "Unknown User"; ?>&nbsp;
                              <?= Txt('ADDED'); ?>: <?= utc_to_tz($row["added"]) ?>
                              <?php if ($USER->has('class') > _UPLOADER) : ?>
                                   <span style='float:right;'>
                                        <a href='<?= Url("team/delete/$row[id]") ?>' title="<?= Txt('DEL') ?>"><i class="fa fa-trash" aria-hidden="true"></i></a>&nbsp;
                                        <a href="javascript:;" class="addAttr" data-bs-toggle="modal" data-bs-target="#addModal" data-id="<?= $row['id'] ?>" data-owner="<?= $row['owner'] ?>" data-name="<?= $row['name'] ?>" data-info="<?= $row['info'] ?>" data-image="<?= $row['image'] ?>">
                                             <i class="fa fa-edit"></i>
                                        </a>
                                   </span>
                              <?php endif; ?>
                         </th>
                    </tr>
               </thead>
               <tbody>
                    <tr>
                         <td>
                              <b><?= Txt('MEMBERS') ?>:</b> <?php
                              if ($row['members']) {
                                   foreach (explode(',', $row['members']) as $member) {
                                        $member = explode(" ", $member); ?>
                                        <?= coloredname($member[0]); ?>&nbsp;<?php
                                   }
                              } ?>
                         </td>
                    </tr>
               </tbody>
          </table>
     </div><br>
<?php } ?>

<?php if ($USER->has('class') > _UPLOADER) : ?>
     <script>
          $('.addAttr').click(function() {
               var id = $(this).data('id');
               var owner = $(this).data('owner');
               var info = $(this).data('info');
               var name = $(this).data('name');
               var image = $(this).data('image');

               $('#id').val(id);
               $('#owner').val(owner);
               $('#info').val(info);
               $('#name').val(name);
               $('#image').val(image);
          });
     </script>

     <!-- Add/Edit Modal -->
     <form id="submit" method="post" action="<?= Url('team/submit') ?>">
          <div class="modal fade" id="addModal">
               <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
                    <div class="modal-content">
                         <div class="modal-body">
                              <input type='hidden' name='id' id="id" />
                              <input id="name" type="text" class="form-control fullwidth" name="name" placeholder="<?= Txt("TEAM") ?>"><br>
                              <input id="owner" type="text" class="form-control fullwidth" name="owner" placeholder="<?= Txt("OWNER") ?>"><br>
                              <input id="info" type="text" class="form-control fullwidth" name="info" placeholder="<?= Txt("DESCRIPTION") ?>"><br>
                              <input id="image" type="text" class="form-control fullwidth" name="image" placeholder="<?= Txt("LOGO_COLOUR") ?>">
                         </div>
                         <div class="modal-footer">
                              <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= Txt("CLOSE") ?></button>
                              <button type="submit" class="btn btn-sm ttbtn"><?= Txt("SUBMIT") ?></button>
                         </div>
                    </div>
               </div>
          </div>
     </form>
<?php endif ?>
<?php block_end() ?>