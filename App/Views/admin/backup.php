<?php block_begin($V_title) ?>
<p class="text-center">
    <a href='<?= Url('adminbackup/submit') ?>'><?= Txt('BACKUP_DB') ?>
</p>
<div class='table-responsive'>
    <table class='table table-striped'>
        <thead>
            <tr>
                <th><b><?= Txt('FILE') ?></b></th>
                <th><b><?= Txt('SIZE') ?></b></th>
                <th><b><?= Txt('DOWNLOAD') ?></b></th>
                <th><b><?= Txt('DEL') ?></b></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($V_files as $row) { ?> 
            <tr>
            <td><?= $row['name'] . "." . $row['ext'] ?></td>
            <td><?= $row['size'] ?> KByte</td>
            <td><a href='<?= Url("adminbackup/download/$row[name].$row[ext]") ?>'><i class='fa fa-download' title='<?= Txt('DOWNLOAD') ?>'></i></a></td>
            <td><a href='<?= Url("adminbackup/delete/$row[name].$row[ext]") ?>'><i class='fa fa-trash' title='<?= Txt('DEL') ?>'></i></a></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php block_end() ?>