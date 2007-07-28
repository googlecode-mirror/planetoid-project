<?php
// 	ignore_user_abort(true);
	error_reporting(0);
	ini_set('session.cache_expire', 20160);  /* 2 weeks */
	ini_set('session.gc_maxlifetime', 1209600);
	ini_set('session.use_only_cookies', 1);
	session_name('planetoid_admin');
	session_start();
	
	if($_GET['ajax'] == 'true') {
		$ajax= true;
	}
	
	if(isset($_SESSION['uid']) && $_SESSION['ulevel'] == 'admin') {
		if(isset($_GET['dir'])) {
			include('../config.php');
			include('../planetoid.php');
			if($ajax) {
				include('plugins-functions.php');
			}
			
			$dir= sql_escape($_GET['dir']);
			
			sql_query("DELETE FROM settings WHERE name='plugin_{$dir}:active';");
			
			if(file_exists("../inc/plugins/{$dir}/deactivate.php")) {
				include_once("../inc/plugins/{$dir}/deactivate.php");
			}
			
			if($ajax) {
				$links= str_replace("'", "\'", generate_manage_links($dir));
				echo "$('#{$dir}-row td:last').html('{$links}').parent().Highlight(500, '#e72300');";
			} else {
				header("Location: {$_GET['r_to']}");
			};
			
			sql_close();
		} else {
			if($ajax) {
				echo 'alert("An error occured.\nTry again later.");';
			} else {
				header("Location: {$_GET['r_to']}?failed=true");
			}
		}
	} else {
		if($ajax) {
			echo "window.location= '../login.php';";
		} else {
			header("Location: ../login.php");
		}
	}
?>