<?php
use App\Models\GuestsModel;
use App\Models\CommentsModel;
use App\Models\MessagesModel;
use App\Models\PeersModel;
use App\Models\TorrentsModel;
use App\Models\UsersModel;

$date_time = get_date_time(gmtime() - (3600 * 24)); // the 24hrs is the hours you want listed
$registered = UsersModel::fetchColumn('count(id)', '');
$ncomments = CommentsModel::fetchColumn('count(id)', '');
$nmessages = MessagesModel::fetchColumn('count(id)', '');
$ntor = TorrentsModel::fetchColumn('count(id)', '');
$totaltoday = UsersModel::fetchColumn('count(id)', ['last_access >='], [$date_time]);
$regtoday = UsersModel::fetchColumn('count(id)', ['added >='], [$date_time]);
$todaytor = TorrentsModel::fetchColumn('count(id)', ['added >='], [$date_time]);
$guests = GuestsModel::clean();
$seeders = PeersModel::fetchColumn('count(id)', ['seeder='], ['yes']);
$leechers = PeersModel::fetchColumn('count(id)', ['seeder='], ['no']);
$members = UsersModel::fetchColumn('count(id)', ['UNIX_TIMESTAMP(?) - UNIX_TIMESTAMP(users.last_access) <'], [get_date_time(), 900]);
$totalonline = $members + $guests;

$totaldownloaded = UsersModel::fetchColumn('SUM(downloaded) AS totaldl', '');
$totaluploaded = UsersModel::fetchColumn('SUM(uploaded) AS totalul', '');

$localpeers = $leechers + $seeders;
if ($USER->has('id') && $USER->permission('edit_users')) {
	block_begin(Txt("STATISTICS")); ?>
	<ul class="list-unstyled">
	<p><strong><?= Txt("TORRENTS") ?></strong></p>
	<li><i class="fa fa-folder-open-o"></i> <?= Txt("TRACKING") ?>: <strong><?= $ntor; ?></strong></li>
	<li><i class="fa fa-calendar-o"></i> <?= Txt("TODAYS_TORRENTS") ?>: <strong><?= $todaytor; ?></strong></li>
	<li><i class="fa fa-refresh"></i> <?= Txt("SEEDERS") ?>: <strong><?= number_format($seeders); ?></strong></li>
	<li><i class="fa fa-arrow-circle-down"></i> <?= Txt("LEECHERS") ?>: <strong><?= number_format($leechers); ?></strong></li>
	<li><i class="fa fa-arrow-circle-up"></i> <?= Txt("PEERS") ?>: <strong><?= $localpeers ?></strong></li>
	<li><i class="fa fa-download"></i> <?= Txt("DOWNLOADED") ?>: <strong><span class="label label-danger"><?= mksize($totaldownloaded); ?></span></strong></li>
	<li><i class="fa fa-upload"></i> <?= Txt("UPLOADED") ?>: <strong><span class="label label-success"><?= mksize($totaluploaded); ?></span></strong></li>
	<hr />
	<p><strong><?= Txt("MEMBERS") ?></strong></p>
	<li><?= Txt("WE_HAVE") ?>: <strong><?= $registered ?></strong></li>
	<li><?= Txt("NEW_TODAY") ?>: <strong><?= $regtoday ?></strong></li>
	<li><?= Txt("VISITORS_TODAY") ?>: <strong><?= $totaltoday ?></strong></li>
	<hr />
	<p><strong><?= Txt("ONLINE") ?></strong></p>
	<li><?= Txt("TOTAL_ONLINE") ?>: <strong><?= $totalonline ?></strong></li>
	<li><?= Txt("MEMBERS") ?>: <strong><?= $members ?></strong></li>
	<li><?= Txt("GUESTS_ONLINE") ?>: <strong><?= $guests ?></strong></li>
	<li><?= Txt("COMMENTS_POSTED") ?>: <strong><?= $ncomments ?></strong></li>
	<li><?= Txt("MESSAGES_SENT") ?>: <strong><?= $nmessages ?></strong></li>
    </ul>
    <?php
	block_end();
}
if (!$USER->permission('edit_users')) {
    block_begin(Txt("STATISTICS"));
	?>
    <ul class="list-unstyled">
	<p><strong><?= Txt("TORRENTS") ?></strong></p>
	<li><i class="fa fa-folder-open-o"></i> <?= Txt("TRACKING") ?>: <strong> <?= Txt("TORRENT". $ntor); ?></strong></li>
	<li><i class="fa fa-calendar-o"></i> <?= Txt("NEW_TODAY") ?>: <strong><?= $todaytor ?></strong></li>
    </ul>
	<?php
	block_end();
}