<?php block_begin($page_title) ?>
<p class="text-center">
   <a href="javascript:;" class="newsAttr" data-bs-toggle="modal" data-bs-target="#newsModal"><?= LANG['ADD'] ?></a>
</p> <?php
if ($V_news->rowCount() > 0) {
   foreach ($V_news as $row) {
      $body = format_comment($row["body"]);
      $added = $row["added"] . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($row["added"]))) . " ago)";
      ?>
      <div class="row  rounded p-3">
         <div class="col">
            <?= $added; ?>&nbsp;---&nbsp;by&nbsp;<?= coloredname($row["userid"]); ?>
            <a href='<?= Url("adminnews/delete/$row[id]") ?>'><i class="fa fa-trash"></i></a>
            <a href="javascript:;" class="newsAttr" data-bs-toggle="modal" data-bs-target="#newsModal" data-id="<?= $row['id'] ?>" data-body="<?= $row['body'] ?>" data-title="<?= $row['title'] ?>">
               <i class="fa fa-edit"></i>
            </a><br>
            <b><?= $row["title"]; ?></b><br /><?= $body; ?>
         </div>
      </div>
      <?php
   }
} else {
   echo "No News Posted";
} ?>
<script>
   $('.newsAttr').click(function() {
      var id = $(this).data('id');
      var body = $(this).data('body');
      var title = $(this).data('title');

      $('#id').val(id);
      $('#body').val(body);
      $('#title').val(title);
   });
</script>
<!-- The Modal In Form -->
<form id="submit" method="post" action="<?= Url('adminnews') ?>">
   <div class="modal fade" id="newsModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
         <div class="modal-content">
            <div class="modal-body">
               <input type='hidden' name='id' id='id'/>
               <?= LANG["TITLE"] ?>
               <input type='text' name='title' id='title' class="form-control fullwidth"/><br />
               <?php echo textbbcode("submit", "body"); ?>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
               <button type="submit" class="btn btn-sm ttbtn"><?= LANG['SUBMIT'] ?></button>
            </div>
         </div>
      </div>
   </div>
</form>
<?php block_end() ?>