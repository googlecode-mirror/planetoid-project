<?php

	include_once('inc/simplepie/idn/idna_convert.class.php');
	include('inc/simplepie/simplepie.inc');
	include('config.php');
	include('planetoid.php');

	
	define('THEME_PATH', 'inc/themes/'.get_setting_value('theme_dir_name'));
	include(THEME_PATH.'/index.php');
	sql_close();
	
?>
