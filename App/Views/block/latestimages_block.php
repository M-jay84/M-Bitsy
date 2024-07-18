<?php
use App\Libs\MDBS;
use App\Models\TorrentsModel;
if (!CONFIG['MEMBERSONLY'] || $USER->has('id')) {
    $res = TorrentsModel::getAll("WHERE visible = ? AND banned = ?", ['yes', 'no']);
    if ($res['count'] > 0) {
        block_begin(Txt("LATEST_POSTERS"));
        print '<table>';
        print '<tr>';
        foreach ( $res['stmt'] as $r )
        {
        print (($i && $i % 2) ? '
        <td><a href="'.Url("torrent/$r[id]").'">
        <img src="' . MDBS::getImage($r) . '" height="100" width="100" border="0" /></a></td>
        </tr>' : '
        <tr><td><a href="'.Url("torrent/$r[id]").'">
        <img src="' . MDBS::getImage($r) . '" height="100" width="80" border="0" /></a></td>');
        $i++;
        if ($i > 7) break;
        } 
        print '</tr>';
        print '</table>';
        block_end();
    }
}