<?php block_begin($V_title) ?>
<p class="text-center">
   <?= Txt('POLLS') ?>
   <a href="javascript:;" class="pollAttr" data-bs-toggle="modal" data-bs-target="#pollModal"><?= Txt("ADD") ?></a>
</p>

<?php foreach ($V_polls as $row) : ?>
   <div class='tt_blockcenter rounded p-3'>
      <b><?= stripslashes($row['question']) ?></b> -
      <?= utc_to_tz($row['added']); ?> -
      <a href='<?= Url("adminpoll/delete/$row[id]") ?>'>
         <i class="fa fa-trash"></i>
      </a>
      <a href="javascript:;" class="pollAttr" data-bs-toggle="modal" data-bs-target="#pollModal" data-id="<?= $row['id'] ?>" data-question="<?= $row['question'] ?>" data-option0="<?= $row['option0'] ?>" data-option1="<?= $row['option1'] ?>" data-option2="<?= $row['option2'] ?>" data-option3="<?= $row['option3'] ?>" data-option4="<?= $row['option4'] ?>" data-option5="<?= $row['option5'] ?>" data-option6="<?= $row['option6'] ?>" data-option7="<?= $row['option7'] ?>" data-option8="<?= $row['option8'] ?>" data-option9="<?= $row['option9'] ?>" data-option10="<?= $row['option10'] ?>" data-option11="<?= $row['option11'] ?>" data-sort="<?= $row['sort'] ?>">
         <i class="fa fa-edit"></i>
      </a><br>
      <?php $results = pollresults($row['id']); ?>
      <div class="table-responsive  p-3">
         <table class='table table-striped'>
         <thead>
            <tr>
                <th><?= Txt('NAME') ?></th>
                <th><?= Txt('VOTED') ?></th>
            </tr>
         </thead>
         <tbody>
         <?php foreach ($results as $row) : ?>
            <tr>
               <td><b><a href="<?= Url("profile/$row[userid]") ?>"><?= coloredname($row['userid']); ?></a></b></td>
               <td><?= $row['vote'] ?></td>
            </tr>
         <?php endforeach; ?>
         </tbody>
         </table>
      </div>
   </div><br>
<?php endforeach; ?>

<script>
   $('.pollAttr').click(function() {
      var id = $(this).data('id');
      var question = $(this).data('question');
      var option0 = $(this).data('option0');
      var option1 = $(this).data('option1');
      var option2 = $(this).data('option2');
      var option3 = $(this).data('option3');
      var option4 = $(this).data('option4');
      var option5 = $(this).data('option5');
      var option6 = $(this).data('option6');
      var option7 = $(this).data('option7');
      var option8 = $(this).data('option8');
      var option9 = $(this).data('option9');
      var option10 = $(this).data('option10');
      var option11 = $(this).data('option11');
      var sort = $(this).data('sort');

      $('#id').val(id);
      $('#question').val(question);
      $('#option0').val(option0);
      $('#option1').val(option1);
      $('#option2').val(option2);
      $('#option3').val(option3);
      $('#option4').val(option4);
      $('#option5').val(option5);
      $('#option6').val(option6);
      $('#option7').val(option7);
      $('#option8').val(option8);
      $('#option9').val(option9);
      $('#option10').val(option10);
      $('#option11').val(option11);
      $('#sort').val(sort);
   });
</script>

<!-- Add/Edit Modal -->
<form id="submit" method="post" action="<?= Url('adminpoll/submit') ?>">
   <div class="modal fade" id="pollModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
         <div class="modal-content">
            <div class="modal-body">
               <input type='hidden' name='id' id='id' />
               <div class="input-group mb-3">
                  <span></span>
               </div>
               <?= Txt("QUESTION"); ?> *&nbsp;<input name='question' id='question' /><br><br>
               <?= Txt("OPTION") ?> 1 *&nbsp;<input name='option0' id='option0' /><br><br>
               <?= Txt("OPTION") ?> 2 *&nbsp;<input name="option1" id="option1" /><br><br>
               <?= Txt("OPTION") ?> 3&nbsp;&nbsp;&nbsp;<input name="option2" id="option2" id="option2" /><br><br>
               <?= Txt("OPTION") ?> 4&nbsp;&nbsp;&nbsp;<input name="option3" id="option3" /><br><br>
               <?= Txt("OPTION") ?> 5&nbsp;&nbsp;&nbsp;<input name="option4" id="option4" /><br><br>
               <?= Txt("OPTION") ?> 6&nbsp;&nbsp;&nbsp;<input name="option5" id="option5" /><br><br>
               <?= Txt("OPTION") ?> 7&nbsp;&nbsp;&nbsp;<input name="option6" id="option6" /><br><br>
               <?= Txt("OPTION") ?> 8&nbsp;&nbsp;&nbsp;<input name="option7" id="option7" /><br><br>
               <?= Txt("OPTION") ?> 9&nbsp;&nbsp;&nbsp;<input name="option8" id="option8" /><br><br>
               <?= Txt("OPTION") ?> 10&nbsp;<input name="option9" id="option9" /><br><br>
               <?= Txt("OPTION") ?> 11&nbsp;<input name="option10" id="option10" /><br><br>
               <?= Txt("OPTION") ?> 12&nbsp;<input name="option11" id="option11" /><br><br>
               <?= Txt("SORT") ?>&nbsp;
               <input type="radio" name="sort" value="yes" id="sort" /><?= Txt("YES") ?>
               <input type="radio" name="sort" value="no" id="sort" /><?= Txt("NO") ?>
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