<?php
ini_set('session.cache_expire', 20160);  /* 2 weeks */
ini_set('session.gc_maxlifetime', 1209600);
ini_set('session.use_only_cookies', 1);
session_name('planetoid_admin');
session_start();

if(isset($_SESSION['uid']) && isset($_SESSION['ulevel'])) {
	require('../config.php');
	require('../planetoid.php');
	require('feeds-functions.php');
	$curr_page= basename(__FILE__);
	include('header.php');
?>
		<div id="page">
			<div id="page-area">
				<?php if($_GET['done'] == 'true'): ?>
				<div id="updated">
					Settings have been saved.
				</div>
				<?php endif; ?>
				<h2><img src="inc/images/loading.gif" id="loading" alt="loading" style="display: none;" />Feeds</h2>
				<a name="manage"></a>
				<h3>Manage feeds</h3>
				<div class="info">
					Here you can add, remove, reject and approve feeds.
				</div>
				<p class="settings">
					<?=make_feed_table()?>
				</p>
				<br/>
<!-- 				<input type="submit" value="Add feed &raquo;" class="settings-submit" /> -->
				<a name="add"></a>
				<h3>Add new feed</h3>
				<?php if($_GET['e'] == 'not-all'): ?>
				<div id="error">
					You have to fill all fields
				</div>
				<?php endif; ?>
				<form action="add-feed.php" method="POST" id="add-feed" onsubmit="Feeds.add();return false;" enctype="multipart/form-data">
					<p class="settings">
						<label for="url">Feed URL:</label>
						<input type="text" name="url" id="url" value="http://" />
						
						<label for="avatar">Hackergotchi (max 0.5 MB):</label>
						<input type="hidden" name="MAX_FILE_SIZE" value="524288" /><!-- 0.5 mb -->
						<input type="file" name="avatar" id="avatar" />
						
						<label for="email">Where to send notifications (author&#700;s email):</label>
						<input type="text" name="email" id="email" />
						
						<label for="approved">Status:</label>
						<select name="approved" id="approved">
							<option value="1">Approved</option>
							<option value="0">Waiting for approval</option>
							<option value="2">Hidden</option>
						</select>
					</p>
					<p class="settings">
						<input type="hidden" value="<?=$curr_page?>" name="r_to" />
						<input type="submit" value="Add feed &raquo;" />
						<input type="reset" value="Reset form" />
					</p>
				</form>
				<br/>
				<a name="filters"></a>
				<h3>Filters</h3>
				<p class="info">
					Using <a href="http://en.wikipedia.org/wiki/Regexp" target="_blank">regular expressions</a> you can filter articles by their title and/or content, leave boxes empty if you don't wish to use filters.<br/>
					If you are using both filters and article matches only one, article will be shown.
				</p>
				<form action="setting-set.php" method="POST">
					<p class="settings">
						<label for="title_regexp">Filter articles by title:</label>
						<input type="text" name="title_regexp" id="title_regexp" value="<?=get_setting_value('title_regexp')?>" />
						
						<label for="content_regexp">Filter articles by content:</label>
						<input type="text" name="content_regexp" id="content_regexp" value="<?=get_setting_value('content_regexp')?>" />
						
						<label for="posts_num">How many posts to show on homepage:</label>
						<select id="posts_num" name="posts_num">
							<?php
							$post_nums= array('All', 10, 25, 45, 50, 75, 100);
							$current_num= get_setting_value('posts_num');
							for($n=0; $n < count($post_nums); $n++) {
								if($n == 0) {
									$num= 0;
								} else {
									$num= $post_nums[$n];
								}
								
								if($num == $current_num) {
									echo "<option value=\"$num\" selected=\"selected\">{$post_nums[$n]}</option>";
								} else {
									echo "<option value=\"$num\">{$post_nums[$n]}</option>";
								}
							}
							?>
						</select>
					</p>
					
					<p class="settings">
						<input type="hidden" value="<?=$curr_page?>" name="r_to" />
						<input type="submit" value="Save &raquo;" />
					</p>
				</form>
				</div>
		</div>
<?php
	include('footer.php');
	sql_close();
} else {
	header("Location: ../login.php");
}
?>
