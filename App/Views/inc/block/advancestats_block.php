<?php
use App\Libs\DB;

$date_time = get_date_time(gmtime() - (3600 * 24)); // the 24hrs is the hours you want listed
$registered = DB::fetchColumn('users', 'count(id)', '');
$ncomments = DB::fetchColumn('comments', 'count(id)', '');
$nmessages = DB::fetchColumn('messages', 'count(id)', '');
$ntor = DB::fetchColumn('torrents', 'count(id)', '');
$totaltoday = DB::fetchColumn('users', 'count(id)', ['last_access >='], [$date_time]);
$regtoday = DB::fetchColumn('users', 'count(id)', ['added >='], [$date_time]);
$todaytor = DB::fetchColumn('torrents', 'count(id)', ['added >='], [$date_time]);
$past = (gmtime() - 2400);
$stmt =  DB::run("DELETE FROM `guests` WHERE `time` < ?", [$past]);
$guests = DB::fetchColumn('guests', 'count(ip)', '');
$seeders = DB::fetchColumn('peers', 'count(id)', ['seeder='], ['yes']);
$leechers = DB::fetchColumn('peers', 'count(id)', ['seeder='], ['no']);
$members = DB::fetchColumn('users', 'count(id)', ['UNIX_TIMESTAMP(?) - UNIX_TIMESTAMP(users.last_access) <'], [get_date_time(), 900]);
$totalonline = $members + $guests;

$totaldownloaded = DB::fetchColumn('users', 'SUM(downloaded) AS totaldl', '');
$totaluploaded = DB::fetchColumn('users', 'SUM(uploaded) AS totalul', '');

$localpeers = $leechers + $seeders;
if ($_SESSION['id'] && $_SESSION['edit_users'] == 'yes') {
	block_begin(LANG["STATISTICS"]); ?>
	<ul class="list-unstyled">
	<p><strong><?= LANG["TORRENTS"] ?></strong></p>
	<li><i class="fa fa-folder-open-o"></i> <?= LANG["TRACKING"] ?>: <strong><?= $ntor; ?></strong></li>
	<li><i class="fa fa-calendar-o"></i> <?= LANG["TODAYS_TORRENTS"] ?>: <strong><?= $todaytor; ?></strong></li>
	<li><i class="fa fa-refresh"></i> <?= LANG["SEEDERS"] ?>: <strong><?= number_format($seeders); ?></strong></li>
	<li><i class="fa fa-arrow-circle-down"></i> <?= LANG["LEECHERS"] ?>: <strong><?= number_format($leechers); ?></strong></li>
	<li><i class="fa fa-arrow-circle-up"></i> <?= LANG["PEERS"] ?>: <strong><?= $localpeers ?></strong></li>
	<li><i class="fa fa-download"></i> <?= LANG["DOWNLOADED"] ?>: <strong><span class="label label-danger"><?= mksize($totaldownloaded); ?></span></strong></li>
	<li><i class="fa fa-upload"></i> <?= LANG["UPLOADED"] ?>: <strong><span class="label label-success"><?= mksize($totaluploaded); ?></span></strong></li>
	<hr />
	<p><strong><?= LANG["MEMBERS"] ?></strong></p>
	<li><?= LANG["WE_HAVE"] ?>: <strong><?= $registered ?></strong></li>
	<li><?= LANG["NEW_TODAY"] ?>: <strong><?= $regtoday ?></strong></li>
	<li><?= LANG["VISITORS_TODAY"] ?>: <strong><?= $totaltoday ?></strong></li>
	<hr />
	<p><strong><?= LANG["ONLINE"] ?></strong></p>
	<li><?= LANG["TOTAL_ONLINE"] ?>: <strong><?= $totalonline ?></strong></li>
	<li><?= LANG["MEMBERS"] ?>: <strong><?= $members ?></strong></li>
	<li><?= LANG["GUESTS_ONLINE"] ?>: <strong><?= $guests ?></strong></li>
	<li><?= LANG["COMMENTS_POSTED"] ?>: <strong><?= $ncomments ?></strong></li>
	<li><?= LANG["MESSAGES_SENT"] ?>: <strong><?= $nmessages ?></strong></li>
    </ul>
    <?php
	block_end();
}
if (!$_SESSION['edit_users'] == 'yes') {
    block_begin(LANG["STATISTICS"]);
	?>
    <ul class="list-unstyled">
	<p><strong><?= LANG["TORRENTS"] ?></strong></p>
	<li><i class="fa fa-folder-open-o"></i> <?= LANG["TRACKING"] ?>: <strong> <?= LANG["TORRENT". $ntor]; ?></strong></li>
	<li><i class="fa fa-calendar-o"></i> <?= LANG["NEW_TODAY"] ?>: <strong><?= $todaytor ?></strong></li>
    </ul>
	<?php
	block_end();
}