<?php
// 	ignore_user_abort(true);
	ini_set('session.cache_expire', 20160);  /* 2 weeks */
	ini_set('session.gc_maxlifetime', 1209600);
	ini_set('session.use_only_cookies', 1);
	session_name('planetoid_admin');
	session_start();
	
	if($_GET['ajax'] == 'true') {
		$ajax= true;
	}
	
	if(isset($_SESSION['uid']) && $_SESSION['ulevel'] == 'admin') {
		$id= $_GET['id'];
		
		if(isset($id)) {
			include('../inc/simplepie/idn/idna_convert.class.php');
			include('../inc/simplepie/simplepie.inc');
			include('../config.php');
			include('../planetoid.php');
			if($ajax) {
				include('feeds-functions.php');
			}
			
			$id=sql_escape($id);
			$current= sql_action("SELECT * FROM feeds WHERE id='$id';");
			$curr_status= $current['approved'];
			if($curr_status == 2) {
				$to_n= 1;
				$effect= "$('#table-row-$id').fadeTo(400, 1);";
				add_feed($current);
			} else if($curr_status == 1) {
				$to_n= 2;
				$effect= "$('#table-row-$id').fadeTo(400, 0.4);";
				remove_feed($id);
			};
			
			sql_query("UPDATE feeds SET approved='$to_n' WHERE id='$id';");
			
			if($ajax) {
				$links= generate_manage_links($id, $to_n);
				echo $effect;
				echo "$('#table-row-$id td:last').html('{$links['manage']}');";
			} else {
				header("Location: {$_GET['r_to']}");
			};
			
// 			exit;
			
			refresh_cache();
			
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