<?php
ini_set('session.cache_expire', 20160);  /* 2 weeks */
ini_set('session.gc_maxlifetime', 1209600);
ini_set('session.use_only_cookies', 1);
session_name('planetoid_admin');
session_start();

if(isset($_SESSION['uid']) && $_SESSION['ulevel'] == 'admin' && isset($_GET['id'])) {
	require('../config.php');
	require('../planetoid.php');
	require('looks-functions.php');
	$curr_page= basename(__FILE__);
	include('header.php');
?>
		<div id="page">
			<div id="page-area">
				<?php 
					if($_GET['done'] == 'true') {
				?>
					<div id="updated">
						Settings have been saved.
					</div>
				<?php }; ?>
				<form action="update-feed.php" method="POST">
				<input type="submit" value="Save changes &raquo;" class="settings-submit" />
				<h2><img src="inc/images/loading.gif" id="loading" alt="loading" style="display: none;" />Edit feed</h2>
				<hr style="display:none;"/>
				<a name="details"></a>
				<h3>Feed details</h3>
				<?php $feed= sql_action("SELECT * FROM feeds WHERE id='".sql_escape($_GET['id'])."';"); ?>
					<img src="../<?php echo $feed['avatar']; ?>" id="feed-avatar" alt="Hackergotchi" />
					<p class="settings" style="padding-left: 70px;">
						<input type="hidden" name="id" value="<?php echo $feed['id']; ?>" />
						<input type="hidden" name="r_to" value="<?php echo $curr_page.'?id='.$feed['id']; ?>" />
						
						<label for="url">Feed URL:</label>
						<input type="text" name="url" id="url" value="<?php echo $feed['url']; ?>" />
						
						<label for="email">Submitters email</label>
						<input type="text" name="email" id="email" value="<?php echo $feed['email']; ?>" />
						
						<label for="approved">Status:</label>
						<select name="approved" id="approved">
							<?php
							$feed_statuses= array('Waiting for approval', 'Approved', 'Hidden');
							
							for($n=0; $n < 3; $n++) {
								if($n == $feed['approved']) {
									echo "<option value=\"$n\" selected=\"selected\">{$feed_statuses[$n]}</option>";
								} else {
									echo "<option value=\"$n\">{$feed_statuses[$n]}</option>";
								}
							}
							?>
						</select>
						<!--<a href="remove-feed.php?id=<?php echo $_GET['id']; ?>&amp;to=<?php echo $curr_page.'?id='.$feed['id']; ?>" class="action-link link-red" onclick="Feeds.remove(<?php echo $_GET['id']; ?>);return false;">Delete feed</a>--->
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
