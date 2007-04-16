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
				<?php 
					if($_GET['done'] == 'true') {
				?>
					<div id="updated">
						Settings have been saved.
					</div>
				<?php }; ?>
				<h2><img src="inc/images/loading.gif" id="loading" alt="loading" style="display: none;" />Looks</h2>
				<h3>Themes</h3><div class="info">
					Change the look of your planet with just a few clicks! With Planetoid themes it's really easy!
				</div>
				<div id="themes">
					<?php make_theme_list(); ?>
					<br style="clear:both;"/>
				</div>
			</div>
		</div>
<?php
	include('footer.php');
	sql_close();
} else {
	header("Location: ../login.php");
}

?>
