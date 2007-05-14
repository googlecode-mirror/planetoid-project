<?php
	
	/* If PHP shorthands (<?=[...]?>) are disabled, enable them */
	ini_set("short_open_tag", true);
	
	/* Is this a mobile phone/PDA or deskop computer? */
	if(strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'midp')) {
		$MOBILE= true;
	} else {
		$MOBILE= false;
	}
	
	/*  */
	ini_set("user_agent", "Planetoid (http://planetoid-project.org/)");
	
	/* Load SimplePie, Planetoid configuration & Main Planetoid file */
	require_once('inc/simplepie/idn/idna_convert.class.php');
	require_once('inc/simplepie/simplepie.inc');
	require('config.php');
	require('planetoid.php');

	/* Load plugins */
	$plugins= list_active_plugins();
	for($p=0; $p < count($plugins); $p++) {
		$plugin_load_file= plugin_load_file($plugins[$p]);
		if(file_exists($plugin_load_file)) {
			include($plugin_load_file);
		}
	}
	
	/* Plugin checkpoint: Header */
	checkpoint("header");
	
	/* Load user defined theme, or if it's mobile/PDA browser load special theme */
	if(!$MOBILE) {
		define('THEME_PATH', 'inc/themes/'.get_setting_value('theme_dir_name'));
		include(THEME_PATH.'/index.php');
	} else {
		if(isset($_GET['p'])) {
			$page= intval($_GET['p']);
		} else {
			$page= 1;
		}
		
		include('inc/themes/mobile/index.php');
	}
	
	/* Plugin checkpoint: Footer */
	checkpoint("footer");
	
	sql_close();
?>
