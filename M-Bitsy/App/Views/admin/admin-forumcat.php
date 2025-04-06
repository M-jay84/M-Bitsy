<?php block_begin($TITLE) ?>
 <div class="rounded">
     <div class="text-center">
        <b><?= LANG["FORUM_CATAGORY"] ?></b><br>
        <a type="button" class="model_form ttbtn btn-sm" data-bs-toggle="modal" data-bs-target="#forumcatModal"><?= LANG["ADD"] ?></a>
     </div>
     <form id="submit" method="post" action="<?= Url('adminforumcat/submit') ?>">
         <div class="table-responsive rounded rounded-bottom">
             <table class="table table-striped">
                 <thead>
                     <tr>
                         <th><?= LANG["ID"] ?></th>
                         <th><?= LANG["TITLE"] ?></th>
                         <th><?= LANG["SORT"] ?></th>
                         <th><?= LANG["MOVE"] ?></th>
                         <th></th>
                     </tr>
                 </thead>
                 <tbody>
                     <?php foreach ($CATEGORY as $row) : ?>
                         <tr>
                             <td><?= $row['id'] ?></td>
                             <td><a href='<?= Url("adminforum/$row[id]") ?>'><?= $row['name'] ?></a></td>
                             <td><?= $row['sort'] ?></td>
                             <td>
                                 <?php
                                    $move = $row['sort'] == 1 ? "<a href='adminforumcat/$row[id]/down&sort=$row[sort]'><i class='fa fa-arrow-down'></i></a>" : "<a href='adminforumcat/$row[id]/up&sort=$row[sort]'><i class='fa fa-arrow-up'></i></a>  <a href='adminforumcat/$row[id]/down&sort=$row[sort]'><i class='fa fa-arrow-down'></i></a>";
                                    echo $move
                                    ?>
                             </td>
                             <td>
                                 <a href="javascript:;" class="forumcatAttr" data-bs-toggle="modal" data-bs-target="#forumcatModal" data-id="<?= $row['id'] ?>" data-name="<?= $row['name'] ?>" data-sort="<?= $row['sort'] ?>">
                                     <i class="fa fa-edit"></i>
                                 </a>
                                 <a href='<?= Url("adminforumcat/delete/$row[id]") ?>'>
                                     <i class='fa fa-trash' title='<?= LANG["DEL"]  ?>'></i>
                                 </a>
                             </td>
                         </tr>
                    <?php endforeach; ?>
                 </tbody>
             </table>
         </div>
     </form>
 </div>

 <script>
     $('.forumcatAttr').click(function() {
         var id = $(this).data('id');
         var name = $(this).data('name');
         var sort = $(this).data('sort');

         $('#id').val(id);
         $('#name').val(name);
         $('#sort').val(sort);
     });
 </script>
<!-- Add/Edit Modal -->
 <form id="submit" method="post" action="<?= Url('adminforumcat/submit') ?>">
     <div class="modal fade" id="forumcatModal" tabindex="-1" aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
             <div class="modal-content">
                 <div class="modal-body">
                     <input type='hidden' name='id' id='id' />
                    <input class="form-control fullwidth" type='text' name='name' id='name' placeholder="<?= LANG["NAME"] ?>"><br>
                    <input class="form-control fullwidth" type='number' name="sort" id='sort' placeholder="<?= LANG["SORT"] ?>"><br>
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