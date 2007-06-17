<?php
	ini_set("short_open_tag", true);
	ini_set("user_agent", "Planetoid (http://planetoid-project.org/)");
	
	if(strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'midp')) {
		define('MOBILE', true);
	} else {
		define('MOBILE', false);
	}
	
	require_once('inc/simplepie/idn/idna_convert.class.php');
	require_once('inc/simplepie/simplepie.inc');
	require('config.php');
	require('planetoid.php');

	$plugins= list_active_plugins();
	
	for($p=0; $p < count($plugins); $p++) {
		$plugin_load_file= plugin_load_file($plugins[$p]);
		if(file_exists($plugin_load_file)) {
			include($plugin_load_file);
		}
	}
	
	checkpoint("header");
	
	if(!MOBILE) {
		define('THEME_PATH', 'inc/themes/'.get_setting_value('theme_dir_name'));
		include(THEME_PATH.'/index.php');
	} else {
		checkpoint("mobile.header");
		
		if(isset($_GET['p'])) {
			$page= intval($_GET['p']);
		} else {
			$page= 1;
		}
		
		include('inc/themes/mobile/index.php');
		checkpoint("mobile.footer");
	}
	
	checkpoint("footer");
	
	sql_close();
?>
