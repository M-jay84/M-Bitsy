<?php

use App\Libs\MDBS;
use App\Models\TorrentsModel;

if ($USER->has('id')) {
$tcat = implode(',', PopularCats);
$i = 0;

$q = TorrentsModel::popular($tcat);

block_begin(Txt('POPULAR'));
print '<table>';
print '<tr>';
foreach ( $q as $r )
{
	print (($i && $i % 2) ? '
	<td><a href="'.Url("torrent/$r[id]").'">
	<img src="' . MDBS::getImage($r) . '" height="100" width="100" border="0" /></a></td>
	</tr>' : '
	<tr><td><a href="'.Url("torrent/$r[id]").'">
	<img src="' . MDBS::getImage($r) . '" height="100" width="80" border="0" /></a></td>');
	$i++;
} 
print '</tr>';
print '</table>';
block_end();
}