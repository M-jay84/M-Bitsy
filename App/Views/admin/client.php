<?php block_begin($page_title) ?>
<div class='text-center'>
    <a href="<?= Url('adminclient/peers') ?>"><?= LANG["Current_Clients"] ?></a>
    <a href="<?= Url('adminclient/client') ?>"><?= LANG["Banned Clients"] ?></a>&nbsp;
</div>
<?php if ($V_type == 'clients') : ?>
   <div class="text-center">
      <a type="button" class="model_form ttbtn btn-sm" data-bs-toggle="modal" data-bs-target="#clientModal"><?= LANG['ADD'] ?></a><br />
   </div>
<?php endif; ?>
<div class='table-responsive p-3'>
   <table class='table table-striped'>
      <thead>
         <tr>
            <th><?= LANG["CLIENT"] ?></th>
            <th><?= LANG["PEER_ID"] ?></th>
            <th><?= LANG["TO_BAN"] ?></th>
            <th><?= LANG["BANNED"] ?></th>
            <th><?= LANG["ACTION"] ?></th>
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
               <input type="text" name="peer_id" id="peer_id" class="form-control fullwidth"placeholder="<?= LANG["ENTER_CODE"] ?>">
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG["CLOSE"] ?></button>
               <button type="submit" class="btn btn-sm ttbtn"><?= LANG['SUBMIT'] ?></button>
            </div>
         </div>
      </div>
   </div>
</form>
<?php block_end() ?>