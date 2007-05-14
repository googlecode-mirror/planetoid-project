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
		if(strlen($_POST['url']) > 10 && strlen($_POST['email']) > 5 && isset($_POST['approved'])) {
			include('../config.php');
			include('../planetoid.php');
			
			if($ajax) {
				include('feeds-functions.php');
			}
			
			if(isset($_FILES['avatar'])) {
				$avatar_flnm= basename($_FILES['avatar']['name']);
				$avatar_name= substr(md5($avatar_flnm.time()), 0, 6);
				$ext= explode('.', $avatar_flnm);
				
				$avatar= "avatars/{$avatar_name}.{$ext[1]}";
				
				if(!move_uploaded_file($_FILES['avatar']['tmp_name'], '../'.$avatar)) {
					$avatar= 'inc/images/no-avatar.png';
				}
			} else {
				$avatar= 'inc/images/no-avatar.png';
			}
			
			$insert= sql_query("INSERT INTO feeds VALUES (".sql_autoid('feeds').", '".sql_escape($_POST['url'])."', '".sql_escape($_POST['email'])."', '$avatar', ".sql_escape($_POST['approved']).", '".date('Y-m-d')."');");
			
			if(!$insert) {
				if($ajax) {
					echo "alert('An error occured.');";
				} else {
					header("Location: {$_POST['r_to']}?e=true");
				}
				exit(1);
			}
			
			if($ajax) {
				sleep(1);
				$id= sql_action("SELECT id FROM feeds WHERE url='".sql_escape($_POST['url'])."';");
				$id= $id['id'];
				
				$links= generate_manage_links($id, $feed['approved']);
				$manage= $links['manage'];
				$new_note= $links['new_note'];
				$hidden= $links['hidden'];
				
				$table_row= "<tr$hidden id=\"table-row-$id\"><td class=\"num\">$id $new_note</td><td><a href=\"{$feed['url']}\" target=\"_blank\">{$feed['url']}</a></td><td>$manage</td></tr>\n";
				echo "$('#feeds-table tbody').append('$table_row');$('#feeds-table tbody tr:last').Highlight(1000, '#ffe');";
			} else {
				header("Location: {$_POST['r_to']}");
			}
			
			sql_close();
			refresh_cache();
	} else {
			if($ajax) {
				echo 'alert("You have to fill all fields execept avatar\'s URL.");';
			} else {
				header("Location: ".r_to(0)."?e=not-all#".r_to(1));
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