<?php block_begin($V_title) ?>
<div class='table-responsive  p-2'>
    <table class='table table-striped'>
        <thead>
            <tr>
                <th><?= Txt("USER") ?></th>
                <th><?= Txt("CLASS") ?></th>
                <th><?= Txt("REGISTERED") ?></th>
                <th><?= Txt("LAST_ACCESS") ?></th>
                <th><?= Txt("DOWNLOADED") ?></th>
                <th><?= Txt("UPLOADED") ?></th>
                <th><?= Txt("RATIO") ?></th>
                <th><?= Txt("WARNED") ?></th>
            </tr>
        </thead>
        <tbody> <?php
        for ($i = 1; $i <= $V_num; $i++) {
            $arr = $V_res->fetch(PDO::FETCH_ASSOC);
            if ($arr["invited_by"] != $USER->has('id') && $USER->has('class') < 5) {
                Redirect(Url("profile/$V_id"), Txt("NO_PERMISSION"));
             }
            if ($arr['added'] == '0000-00-00 00:00:00') {
                $arr['added'] = '---';
            }
            if ($arr['last_access'] == '0000-00-00 00:00:00') {
                $arr['last_access'] = '---';
            }
            $userratio = $arr["downloaded"] > 0 ? number_format($arr["uploaded"] / $arr["downloaded"], 1) : "---";
            if ($arr["warned"] !== "yes") {
                $warned = "<font color=limegreen><b>".Txt("NO")."</b></font>";
            } else {
                $warned = "<font color=red><b>".Txt("YES")."</b></font>";
            }
            $class = get_user_class_name($arr["class"]);
            $added = substr($arr['added'], 0, 10);
            $last_access = substr($arr['last_access'], 0, 10);
            $downloaded = mksize($arr["downloaded"]);
            $uploaded = mksize($arr["uploaded"]); ?>
            <tr>
            <td><b><?= coloredname($arr['id']); ?></b></td>
            <td><?= $class; ?></td>
            <td><?= $added; ?></td>
            <td><?= $last_access; ?></td>
            <td><?= $downloaded; ?></td>
            <td><?= $uploaded; ?></td>
            <td><?= get_ratio_color($userratio); ?></td>
            <td><?= $warned; ?></td>
            </tr> <?php
        } ?>
        </tbody>
    </table>
</div> <?php
block_end();