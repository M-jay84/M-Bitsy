<?php

use App\Models\TmdbLogic;
use App\Models\TorrentsLogic;

if ($_SESSION['id']) {
$tcat = implode(',', CONFIG['PopularCats']);
$i = 0;

$q = TorrentsLogic::popular($tcat);

block_begin(LANG['POPULAR']);
print '<table>';
print '<tr>';
foreach ( $q as $r )
{
	print (($i && $i % 2) ? '
	<td><a href="'.Url("torrent/$r[id]").'">
	<img src="' . TmdbLogic::getImage($r) . '" height="100" width="100" border="0" /></a></td>
	</tr>' : '
	<tr><td><a href="'.Url("torrent/$r[id]").'">
	<img src="' . TmdbLogic::getImage($r) . '" height="100" width="80" border="0" /></a></td>');
	$i++;
} 
print '</tr>';
print '</table>';
block_end();
}