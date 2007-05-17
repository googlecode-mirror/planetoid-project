<?php
ini_set('session.cache_expire', 20160);  /* 2 weeks */
ini_set('session.gc_maxlifetime', 1209600);
ini_set('session.use_only_cookies', 1);
session_name('planetoid_admin');
session_start();

if(isset($_SESSION['uid']) && isset($_SESSION['ulevel'])) {
	require('../config.php');
	require('../planetoid.php');
	require('plugins-functions.php');
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
				<h2><img src="inc/images/loading.gif" id="loading" alt="loading" style="display: none;" />Plugins</h2>
				<a name="manage"></a>
				<h3>Manage plugins</h3>
				<div class="info">
					Using Plugins you can easily extend your Planetoid website.<br/>
					To install new plugins, extract plugin package to <pre style="display:inline"><?=BASE_DIR?>/inc/plugins</pre>
				</div>
				<p class="settings">
					<?=make_plugins_table()?>
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
