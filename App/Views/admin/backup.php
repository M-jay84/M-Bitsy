<p class="text-center">
    <a href='<?= Url('adminbackup/submit') ?>'><?= LANG['BACKUP_DB'] ?>
</p>
<div class='table-responsive'>
    <table class='table table-striped'>
        <thead>
            <tr>
                <th><b><?= LANG['FILE'] ?></b></th>
                <th><b><?= LANG['SIZE'] ?></b></th>
                <th><b><?= LANG['DOWNLOAD'] ?></b></th>
                <th><b><?= LANG['DEL'] ?></b></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($backup_files as $row) { ?> 
            <tr>
            <td><?= $row['name'] . "." . $row['ext'] ?></td>
            <td><?= $row['size'] ?> KByte</td>
            <td><a href='<?= Url("adminbackup/download/$row[name].$row[ext]") ?>'><i class='fa fa-download' title='<?= LANG['DOWNLOAD'] ?>'></i></a></td>
            <td><a href='<?= Url("adminbackup/delete/$row[name].$row[ext]") ?>'><i class='fa fa-trash' title='<?= LANG['DEL'] ?>'></i></a></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>