<?php
	ini_set('session.cache_expire', 20160);  /* 2 weeks */
	ini_set('session.gc_maxlifetime', 1209600);
	ini_set('session.use_only_cookies', 1);
	session_name('planetoid_admin');
	session_start();
// 	ignore_user_abort(true);
	
	if($_GET['ajax'] == 'true') {
		$ajax= true;
	}
	
	$ids= $_GET['id'];
	
	if(isset($_SESSION['uid']) && $_SESSION['ulevel'] == 'admin') {
		if(isset($ids)) {
			include('../inc/simplepie/idn/idna_convert.class.php');
			include('../inc/simplepie/simplepie.inc');
			include('../config.php');
			include('../planetoid.php');
			
			$ids= explode(',', $ids);
			
			for($n=0; $n < count($ids); $n++) {
				$id= sql_escape($ids[$n]);
				$user= sql_action("SELECT avatar, email FROM feeds WHERE id='{$id}';");
				$avatar= $user['avatar'];
				if($avatar != 'inc/images/no-avatar.png') {
					if(file_exists('../'.$avatar)) {
						unlink('../'.$avatar);
					}
				}
				
				/* sql_query("DELETE FROM users WHERE email='{$user['email']}';"); */
				sql_query("DELETE FROM feeds WHERE id='{$id}';");
				
				if($ajax) {
					echo "$('#table-row-{$id}').css({'color': '#fff', 'background': '#e72300'}).fadeOut(500);";
				}
			}
			
			if(!$ajax) {
				header("Location: {$_GET['r_to']}");
			};
			
			remove_feed($id);
			refresh_cache();
			
			sql_close();
		} else {
			if($ajax) {
				echo 'alert("An error occured while adding feed.\nTry again later.");';
			} else {
				header("Location: {$_GET['r_to']}?failed=true");
			}
		}
	} else {
		if($ajax) {
			echo "window.location='../login.php';";
		} else {
			header("Location: ../login.php");
		}
	}
?>