<?php block_begin($V_title) ?>
<div class='text-center'>
    <a href="<?= Url('adminclient/peers') ?>"><?= Txt("Current_Clients") ?></a>
    <a href="<?= Url('adminclient/client') ?>"><?= Txt("Banned Clients") ?></a>&nbsp;
</div>
<?php if ($V_type == 'clients') : ?>
   <div class="text-center">
      <a type="button" class="model_form ttbtn btn-sm" data-bs-toggle="modal" data-bs-target="#clientModal"><?= Txt('ADD') ?></a><br />
   </div>
<?php endif; ?>
<div class='table-responsive p-3'>
   <table class='table table-striped'>
      <thead>
         <tr>
            <th><?= Txt("CLIENT") ?></th>
            <th><?= Txt("PEER_ID") ?></th>
            <th><?= Txt("TO_BAN") ?></th>
            <th><?= Txt("BANNED") ?></th>
            <th><?= Txt("ACTION") ?></th>
         </tr>
      </thead>
      <tbody>
         <?php foreach ($V_client as $row) : ?>
         <?php
         $peer = hex2bin($row['peer_id']);
         $code = substr($peer, 0, 8);
         $isbanned = "<font color='red'><b>No</b></font>";
         if ($row['banned'] == 'yes') {
            $isbanned = "<font color='green'><b>Yes</b></font>";
         }
         ?>
         <tr>
            <td><?= $row['type'] ?></td>
            <td><?= $peer ?></td>
            <td><?= $code ?></td>
            <td><?= $isbanned ?></td>
            <td> <?php
            if ($row['id'] == 0) { ?>
               <a href="javascript:;" class="clientAttr" data-bs-toggle="modal" data-bs-target="#clientModal" data-peer_id="<?= $row['peer_id'] ?>" data-type="<?= $row['type'] ?>">
                  <i class="fa fa-plus"></i>
               </a> <?php
            } else { ?>
               <a href='<?= Url("adminclient/delete/$row[id]") ?>'><i class="fa fa-trash"></i></a> <?php
            } ?>
            </td>
         </tr>
      <?php endforeach; ?>
      </tbody>
   </table>
</div>

<script>
   $('.clientAttr').click(function() {
      var type = $(this).data('type');
      var peer_id = $(this).data('peer_id');

      $('#type').val(type);
      $('#peer_id').val(peer_id);
   });
</script>

<form id="submit" method="post" action="<?= Url('adminclient/submit') ?>">
   <div class="modal fade" id="clientModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
         <div class="modal-content">
            <div class="modal-body">
               <input type='hidden' name='type' id="type" />
               <input type="text" name="peer_id" id="peer_id" class="form-control fullwidth"placeholder="<?= Txt("ENTER_CODE") ?>">
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