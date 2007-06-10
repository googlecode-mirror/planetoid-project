<?php
ini_set('session.cache_expire', 20160);  /* 2 weeks */
ini_set('session.gc_maxlifetime', 1209600);
ini_set('session.use_only_cookies', 1);
session_name('planetoid_admin');
session_start();

if(isset($_SESSION['uid']) && $_SESSION['ulevel'] == 'admin') {
	require('../config.php');
	require('../planetoid.php');
	require('looks-functions.php');
	$curr_page= basename(__FILE__);
	include('header.php');
?>
		<div id="page">
			<div id="page-area">
				<?php if($_GET['done'] == 'true') { ?>
					<div id="updated">
						Settings have been saved.
					</div>
				<?php }; ?>
				<form action="setting-set.php" method="POST">
					<input type="submit" class="settings-submit" value="Save changes &raquo;" />
					<input type="hidden" name="r_to" value="<?=$curr_page?>" />
					<h2><img src="inc/images/loading.gif" id="loading" alt="loading" style="display: none;" />Planet</h2>
					<a name="settings"></a>
					<h3>Planet settings</h3>
					<p class="settings">
						<label for="title">Planet title:</label>
						<input type="text" name="title" id="title" value="<?=get_title()?>" />
						
						<label for="description">Planet description:</label>
						<textarea name="description" id="description"><?=get_description()?></textarea>
						<?php
						$allow_reg= get_setting_value('show_reg_button');
						$notify= get_setting_value('reg_notify');
						
						if($allow_reg == 'on') {
							$allow_reg= "checked=\"true\"";
						} else {
							$allow_reg= "";
						}
						
						if($notify == 'on') {
							$notify= "checked=\"true\"";
						} else {
							$notify= "";
						}
						
						?>
						
						<label for="show_reg_button">
							<input type="checkbox" name="show_reg_button" id="show_reg_button" <?=$allow_reg?> />Allow anyone to submit feed. 
								<small>You will have to approve it before feed shows up on homepage.</small>
						</label>
							
						<label for="reg_notify">
							<input type="checkbox" name="reg_notify" id="reg_notify" <?=$notify?> />Send me an email when someone submits feed. <small>Email will be sent to administrator only if someone other than him submits a feed.</small></label>
					</p>
				</form>
				<br/>
				<h2>About this Planetoid</h2>
				<a name="statistics"></a>
				<h3>Statistics</h3>
				<p>
					Planetoid version: <?php echo PLANETOID_VERSION.'.'.PLANETOID_REVISION; ?>
					<small>
						<span id="update-result">
						<?php
						if(isset($_GET['v']) && isset($_GET['sv']) && isset($_GET['link'])) {
							if(PLANETOID_VERSION.PLANETOID_REVISION < $_GET['v'].$_GET['sv']) {
								echo "There is newer version of Planetoid, v$V.$SV, you can get it at <a href=\"{$_GET['link']}\">{$_GET['link']}</a>";
							} else {
								echo "You have the latest version";
							}
						}
						
						if($_GET['e'] == 'no-curl') {
							echo "Unable to check for updates";
						}
						?></span>
						&#8210;
						<a href="check-updates.php?r_to=<?php echo $curr_page; ?>" class="action-link" onclick="checkUpdates('#update-result'); return false;">Check for updates</a>
					</small>
					<br/>
					This Planetoid installation has been running since <?php echo running_time(); ?><br/>
					<?php if(get_setting_value('pcron') == 'true') { ?>
					Last cache refresh was on <?=last_refresh('r')?>
					<?php } else { ?>
					Ups! <a href="../cron.php?to=admin/planet.php">Cache cron haven't been started yet!</a>
					<?php } ?><br/>
					Using SimplePie <?php require_once('../inc/simplepie/simplepie.inc');echo simplepie_version(); ?>
				</p>
			</div>
		</div>
<?php
	include('footer.php');
	sql_close();
} else {
	header("Location: ../login.php");
}

?>
