<?php
	
	ignore_user_abort(true);
	ini_set('session.cache_expire', 20160);  /* 2 weeks */
	ini_set('session.gc_maxlifetime', 1209600);
	ini_set('session.use_only_cookies', 1);
	session_name('planetoid_admin');
	session_start();
	
	if($_GET['ajax'] == 'true') {
		$ajax= true;
	}
	
	if(isset($_SESSION['uid']) && $_SESSION['ulevel'] == 'admin') {
		$ids= $_GET['id'];
		
		if(isset($ids)) {
			include('../inc/simplepie/idn/idna_convert.class.php');
			include('../inc/simplepie/simplepie.inc');
			include('../config.php');
			include('../planetoid.php');
			if($ajax) {
				include('feeds-functions.php');
			}
			
			$ids= explode(',', $ids);
			
			for($n=0; $n < count($ids); $n++) {
				$id= sql_escape($ids[$n]);
				
				$current= sql_action("SELECT * FROM feeds WHERE id={$id};");
				$curr_status= $current['approved'];
				
				$class_act= '';
				
				if($_GET['to_n'] == 'a') {
					if($curr_status == 2) {
						$to_n= 1;
					} else if($curr_status < 2) {
						$to_n= 2;
					}
				} else {
					$to_n= intval($_GET['to_n']);
				}
				
				if($to_n == 1) {
					$class_act= 'remove';
					add_feed($current);
				} else {
					$class_act= 'add';
					remove_feed($id);
				}
				
				sql_query("UPDATE feeds SET approved='{$to_n}' WHERE id='{$id}';");
				sleep(1);
				
				if($ajax) {
					$links= generate_manage_links($id, $to_n);
					echo "$('#table-row-{$id} td:last').html('{$links['manage']}').parent().{$class_act}Class('hidden');";
				}
			}
			
			if(!$ajax) {
				header("Location: {$_GET['r_to']}");
			};
			
			exit;
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