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
			require('../config.php');
			require('../planetoid.php');
			if($ajax) {
				require('plugins-functions.php');
			}

			$dir= sql_escape($_GET['dir']);

			sql_query("INSERT INTO settings VALUES (".sql_autoid('settings').", 'plugin_{$dir}:active', 'true');");

			if(file_exists("../inc/plugins/{$dir}/activate.php")) {
				require_once("../inc/plugins/{$dir}/activate.php");
			}

			if($ajax) {
				$links= str_replace("'", "\'", generate_manage_links($dir));
				echo "$('#{$dir}-row td:last').html('{$links}').parent().Highlight(500, '#64b31b');";
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