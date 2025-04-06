<?php block_begin($TITLE) ?>
<form action="<?= Url('rss/submit') ?>" method="post">
	<?= LANG["CATS_LEAVE_BLANK_ALL"] ?><br />
	<?php foreach ($CATAGORYS as $row) : ?>
		<input type="checkbox" name="cats[]" value="<?= $row['id'] ?>" /> <?= htmlspecialchars("$row[parent_cat] - $row[name]") ?><br />
	<?php endforeach; ?><br>
	<input type="number" name="user" /> <?= LANG["USER_id"] ?><br><br>
	<input type="checkbox" name="incldead" value="1" /> <?= LANG["INCLUDE_DEAD"] ?><br><br>
	<input type="submit" class='btn btn-sm ttbtn' value="Get Link" />
</form>
<br />
<p class="text-center">
	<ul>
		Quick information regarding our RSS:
		<li>Our RSS feeds are properly validated by true RSS 2.0 XML Parsing Standards. Visit FeedValidator.org to validate.</li>
		<li>Our feeds display only the latest 50 uploaded Torrents as default.</li>
	</ul>
	</p>
<?php block_end() ?>