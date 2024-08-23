<?php block_begin($page_title) ?>
<?php if ($current_user->class > CONFIG['_UPLOADER']) : // todo (tidy up) ?>
    <!-- Add Section -->
    <p class="text-end">
    <i class="fa fa-user-secret" aria-hidden="true" title="Admin"></i><i class="fa fa-plus m-1" data-bs-toggle="modal" data-bs-target="#sectionModal" title="<?= LANG['ADD'] ?>"></i>
    <p>
<?php endif; ?>

<?php foreach ($V_faqs as $id => $temp) : ?>
    <?php $status = ($V_faqs[$id]['flag'] == "0") ? "<font color=\"red\">Hidden</font>" : "<font color=\"yellow\">Normal</font>"; ?>
    <?php if ($V_faqs[$id]['flag'] == "1") { ?>
        <!-- Section Buutons --> 
        <h5 class='text-center'>
            <?= stripslashes($V_faqs[$id]['title']) ?>
            <?php if ($current_user->class > CONFIG['_UPLOADER']) : ?>
            (<?= $status ?> )
               <i class="fa fa-user-secret" aria-hidden="true" title="Admin"></i>
               <a href="<?= Url("faq/delete/$temp[id]") ?>"><i class="fa fa-trash" title="delete"></i></a>
               <a href="javascript:;" class="itemAttr" data-bs-toggle="modal" data-bs-target="#itemModal" data-i_id="<?= $temp['id'] ?>" data-i_type="new" title="<?= LANG['ADD'] ?>">
                <i class="fa fa-plus"></i></a>
               <a href="javascript:;" class="sectionAttr" data-bs-toggle="modal" data-bs-target="#sectionModal" title="<?= LANG['EDIT'] ?>" data-s_id="<?= $temp['id'] ?>" data-s_title="<?= $temp['title'] ?>" data-s_type="<?= $temp['type'] ?>" data-s_flag="<?= $temp['flag'] ?>">
                <i class="fa fa-edit"></i>
                </a>
            <?php endif; ?>
        </h5>
        <?php if (array_key_exists("items", $V_faqs[$id])) { ?>
            <?php foreach ($V_faqs[$id]['items'] as $vars) : ?>
            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading<?= $vars['id'] ?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $vars['id'] ?>" aria-expanded="true" aria-controls="collapse<?= $vars['id'] ?>">
                            <?= $vars['title'] ?>
                            <?php if ($current_user->class > CONFIG['_UPLOADER']) : ?>
                                <i class="fa fa-user-secret" aria-hidden="true" title="Admin"></i>
                                <a href="<?= Url("faq/delete/$vars[id]") ?>"><i class="fa fa-trash" title="delete"></i></a>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#itemModal<?= $vars['id'] ?>" title="<?= LANG['EDIT'] ?>">
                                <i class="fa fa-edit"></i>
                            </a>
                            <?php endif; ?>
                        </button>
                    </h2>
                    <div id="collapse<?= $vars['id'] ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $vars['id'] ?>" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <?= $vars['answer'] ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($current_user->class > CONFIG['_UPLOADER']) : ?>
            <!-- Edit Item Form -->
            <form id="item" method="post" action="<?= Url('faq/item') ?>">
                <div class="modal fade" id="itemModal<?= $vars['id'] ?>">
                    <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
                        <div class="modal-content">
                            <div class="modal-body">
                                <input type='hidden' name='id' value='<?= $vars['id'] ?>' />
                                <input class="form-control fullwidth" type="text" name="title" value='<?= $vars['title'] ?>' placeholder="<?= LANG["TITLE"] ?>">
                                <textarea class="form-control fullwidth" type="text" name="answer" style='height: 300px;'><?= $vars['answer'] ?></textarea>
                                <?= LANG["STATUS"] ?><br>
                                <select name="flag" id="i_flag" class='form-select' value='<?= $vars['flag'] ?>'>
                                    <option value="1"><?= LANG["NORMAL"] ?></option>
                                    <option value="0"><?= LANG["HIDDEN"] ?></option>
                                    <option value="2"><?= LANG["UPDATED"] ?></option>
                                    <option value="3"><?= LANG["NEW"] ?></option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                                <button type="submit" class="btn btn-sm ttbtn"><?= LANG['SUBMIT'] ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <?php endif; ?>
            <?php endforeach; ?>
        <?php } ?> <br>
    <?php  } ?>
<?php  endforeach; ?>

<?php block_end(); ?>
<?php if ($current_user->class > CONFIG['_UPLOADER']) : ?>
<script>
    $('.sectionAttr').click(function() {
        var s_id = $(this).data('s_id');
        var s_title = $(this).data('s_title');
        var s_type = $(this).data('s_type');
        var s_flag = $(this).data('s_flag');
        $('#s_id').val(s_id);
        $('#s_title').val(s_title);
        $('#s_type').val(s_type);
        $('#s_flag').val(s_flag);
    });
</script>

<script>
    $('.itemAttr').click(function() {
        var i_id = $(this).data('i_id');
        $('#i_id').val(i_id);
    });
</script>

<form id="section" method="post" action=" <?= Url('faq/section') ?>">
    <div class="modal fade" id="sectionModal">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-body">
                    <input type='hidden' name='id' id="s_id" />
                    <input type='hidden' name='type' id="s_type" />
                    <input class="form-control fullwidth" type="text" name="title" id="s_title" placeholder="<?= LANG['TITLE'] ?>"><br>
                    <?= LANG["STATUS"] ?><br>
                    <select name="flag" id="s_flag" class='form-select'>
                        <option value="1"><?= LANG["NORMAL"] ?></option>
                        <option value="0"><?= LANG["HIDDEN"] ?></option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                    <button type="submit" class="btn btn-sm ttbtn"><?= LANG['SUBMIT'] ?></button>
                </div>
            </div>
        </div>
    </div>
</form>

<form id="item" method="post" action="<?= Url('faq/item') ?>">
    <div class="modal fade" id="itemModal">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-body">
                    <input type='hidden' name='id' id="i_id" />
                    <input type='hidden' name='type' value="new" />
                    <input class="form-control fullwidth" type="text" name="title" placeholder="<?= LANG["TITLE"] ?>">
                    <textarea class="form-control fullwidth" type="text" name="answer" style='height: 150px;' placeholder="<?= LANG["ANSWER"] ?>"></textarea>
                    <?= LANG["STATUS"] ?><br>
                    <select name="flag" class='form-select'>
                        <option value="1"><?= LANG["NORMAL"] ?></option>
                        <option value="0"><?= LANG["HIDDEN"] ?></option>
                        <option value="2"><?= LANG["UPDATED"] ?></option>
                        <option value="3"><?= LANG["NEW"] ?></option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                    <button type="submit" class="btn btn-sm ttbtn"><?= LANG['SUBMIT'] ?></button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php endif; ?>