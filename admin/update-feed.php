<?php
	ini_set('session.cache_expire', 20160);  /* 2 weeks */
	ini_set('session.gc_maxlifetime', 1209600);
	ini_set('session.use_only_cookies', 1);
	session_name('planetoid_admin');
	session_start();
	
	
	if(isset($_SESSION['uid']) && $_SESSION['ulevel'] == 'admin') {
		$id= $_POST['id'];
		if(strlen($_POST['url']) > 10 && strlen($_POST['email']) > 5 && strlen($id) != 0) {
			include('../config.php');
			include('../planetoid.php');
			
			$id= sql_escape($id);
			while(list($name, $value) = each($_POST)) {
				if($name != 'r_to' && $name != 'id') {
					sql_query("UPDATE feeds SET $name='$value' WHERE id='$id';");
					sleep(1);
				}
			}
			
			refresh_cache();
			sql_close();
			
			header("Location: ".r_to(0)."&done=true#".r_to(1));
		} else {
			if($ajax) {
				echo 'alert("You have to fill all fields execept avatar\'s URL.");';
			} else {
				header("Location: ".r_to(0)."&e=not-all#".r_to(1));
			}
		}
	} else {
		if($ajax) {
			echo "window.location= '..login.php';";
		} else {
			header('Location: ../login');
		}
	}
	
	function r_to($part=0) {
		$link= explode('#', $_POST['r_to']);
		
		return $link[$part];
	}
	
?>