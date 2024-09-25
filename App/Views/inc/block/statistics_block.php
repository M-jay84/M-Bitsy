<?php
use App\Libs\DB;

$date_time = date("Y-m-d H:i:s", CONFIG['TT_TIME'] - (3600 * 24)); // the 24hrs is the hours you want listed
$users_query = "SELECT count(id) FROM `users`";
$registered = DB::run($users_query)->fetchColumn();
$comments_query = "SELECT count(id) FROM `comments`";
$ncomments = DB::run($comments_query)->fetchColumn();
$messages_query = "SELECT count(id) FROM `messages`";
$nmessages = DB::run($messages_query)->fetchColumn();
$torrents_query = "SELECT count(id) FROM `torrents`";
$ntor = DB::run($torrents_query)->fetchColumn();
$users_query = "SELECT count(id) FROM `users` WHERE last_access >=?";
$totaltoday = DB::run($users_query, [$date_time])->fetchColumn();
$users_query = "SELECT count(id) FROM `users` WHERE added >=?";
$regtoday =DB::run($users_query, [$date_time])->fetchColumn();
$torrents_query = "SELECT count(id) FROM `torrents` WHERE added >=? ";
$todaytor = DB::run($torrents_query, [$date_time])->fetchColumn();
$past = (CONFIG['TT_TIME'] - 2400);
$guest_query = "DELETE FROM `guests` WHERE `time` < ?";
$stmt =  DB::run($guest_query, [$past]);
$guest_query = "SELECT count(ip) FROM `guests`";
$guests = DB::run($guest_query)->fetchColumn();
$peers_query = "SELECT count(id) FROM `peers` WHERE seeder = ?";
$seeders = DB::run($peers_query, ['yes'])->fetchColumn();
$peers_query = "SELECT count(id) FROM `peers` WHERE seeder = ?";
$leechers = DB::run($peers_query, ['no'])->fetchColumn();
$users_query = "SELECT count(id) FROM `users` WHERE UNIX_TIMESTAMP(?) - UNIX_TIMESTAMP(users.last_access) < ?";
$members = DB::run($users_query, [CONFIG['TT_DATE'], 900])->fetchColumn();
$totalonline = $members + $guests;
$users_query = "SELECT SUM(downloaded) AS totaldl FROM `users`";
$totaldownloaded = DB::run($users_query)->fetchColumn();
$users_query = "SELECT SUM(uploaded) AS totalul FROM `users`";
$totaluploaded = DB::run($users_query)->fetchColumn();

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