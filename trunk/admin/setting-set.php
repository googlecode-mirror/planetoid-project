<?php
	ini_set('session.cache_expire', 20160);  /* 2 weeks */
	ini_set('session.gc_maxlifetime', 1209600);
	ini_set('session.use_only_cookies', 1);
	session_name('planetoid_admin');
	session_start();
		 
	if($_POST['ajax'] == 'true') {
		$ajax= true;
	}
	
	
	if(isset($_SESSION['uid']) && $_SESSION['ulevel'] == 'admin') {
		include('../config.php');
		require_once('../inc/simplepie/idn/idna_convert.class.php');
		require_once('../inc/simplepie/simplepie.inc');
		include('../planetoid.php');
		
		if(isset($_POST['r_to'])) {
			if($_POST['r_to'] == 'planet.php') {
				if(!isset($_POST['reg_notify'])) {
					$_POST['reg_notify']= 'off';
				} else {
					$_POST['reg_notify']= 'on';
				}
				
				if(!isset($_POST['show_reg_button'])) {
					$_POST['show_reg_button']= 'off';
				} else {
					$_POST['show_reg_button']= 'on';
				}
			}
		}
		
		$theme= false;
		$refresh_cache= false;
		
		while(list($name, $value) = each($_POST)) {
			if($name != 'r_to' && $name != 'ajax') {
				$name= sql_escape($name);
				$value= sql_escape($value);
				
				sql_query("UPDATE settings SET value='$value' WHERE name='$name';");
				sleep(1);
				
				if($name == 'theme_dir_name') {
					$theme= true;
				} else if($name == 'posts_num' || $name == 'title_regexp' || $name == 'content_regexp') {
					$refresh_cache= true;
				}
			}
		}
		
		if($refresh_cache) {
			refresh_cache();
		}
		
		if($ajax) {
			if($theme) {
				include('looks-functions.php');
				make_theme_list();
				echo "<script type=\"text/javascript\">$('#curr-theme-box').fadeIn(500);</script>";
			} else {
				echo "var updated=true;";
			}
		} else {
			header("Location: {$_POST['r_to']}?done=true");
// 			print_r($_POST);
		}
		
		sql_close();
	} else {
		if($ajax) {
			echo "window.location= '../login.php';";
		} else {
			header("Location: ../login.php");
		}
	}
?>