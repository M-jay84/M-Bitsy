<?php block_begin($page_title) ?>
<div class='rounded p-3'> <?php
    foreach ($V_comments as $row) {
        $title = ($row['title']) ? $row['title'] : $row['name'];
        $comentario = stripslashes(format_comment($row['text']));
        $type = 'Other';
        if ($row['torrent'] > 0) {
            $type = 'Torrent: <a href="' . Url("torrent/$row[torrent]") . '">' . $title . '</a>&nbsp;
                    Posted in <b>' . $row['added'] . '</b> by <a href=\"' . Url("profile/$row[user]") . '">' . coloredname($row['user']) . '</a>
                    [ <a href="' . Url("comment/delete/$row[id]") . '">delete</a> ]';
        }
        if ($row['news'] > 0) {
            $type = 'News: <a href="' . Url("comment/$row[torrent]/news") . '">' . $title . '</a>&nbsp;
                     Posted in <b>' . $row['added'] . '</b> by <a href=\"' . Url("profile/$row[user]") . '">' . coloredname($row['user']) . '</a>
                     [ <a href="' . Url("comment/delete/$row[id]"). '">delete</a> ]';
        } ?>

        <div class='table-responsive'>
            <table class='table table-striped'>
                <thead>
                    <tr>
                        <th><?= $type ?></th>
                    </tr>
                </thead>
                <tr>
                    <td><?= $comentario ?></td>
                </tr>
            </table>
        </div> <?php
    } ?>
</div>
<?= $V_pager ?>
<?php block_end() ?>