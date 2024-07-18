<?php block_begin($V_title) ?>
<div class='row tt_blockcenter rounded '>
    <div class="col">
        <div class="rounded">
            <h6 class="text-center">Notification</h6>
            <dl class="row mb-0">
                <dt class="col-sm-2">Titlt</dt>
                <dd class="col-sm-10"><?= $V_notifications['title'] ?></dd>

                <dt class="col-sm-2">Message</dt>
                <dd class="col-sm-10"><?= $V_notifications['message'] ?></dd>

                <dt class="col-sm-2">Added</dt>
                <dd class="col-sm-10"><?= $V_notifications['added'] ?></dd>

                <dt class="col-sm-2">Delete</dt>
                <dd class="col-sm-10"><a href='<?= Url("notification/delete/$V_notifications[id]") ?>'>Delete</a></dd>

            </dl>
        </div>
    </div>
</div>
<?php block_end() ?>