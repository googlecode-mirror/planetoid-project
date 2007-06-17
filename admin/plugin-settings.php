<?php
$file_path= "../inc/plugins/{$_GET['dir']}/settings.php";

if(!file_exists($file_path)) {
	header("Location: {$_GET['r_to']}");
	exit(1);
}

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
	
	$info_file_path= "../inc/plugins/".sql_escape($_GET['dir'])."/plugin.info";
	$file= fopen($info_file_path, "r");
	$_PLUGIN= fread($file, filesize($info_file_path));
	$_PLUGIN= parse_info_file($_PLUGIN, 'Plugin Info');
	fclose($file);
	
?>
		<div id="page">
			<div id="page-area">
				<?php if($_GET['done'] == 'true'): ?>
				<div id="updated">
					Settings have been saved.
				</div>
				<?php endif; ?>
				<h2><img src="inc/images/loading.gif" id="loading" alt="loading" style="display: none;" />Plugin settings &raquo; <?=$_PLUGIN['PluginName']?></h2>
				<p class="settings"><?php include($file_path); ?></p>
			</div>
		</div>
<?php
	include('footer.php');
	sql_close();
} else {
	header("Location: ../login.php");
}
?>
