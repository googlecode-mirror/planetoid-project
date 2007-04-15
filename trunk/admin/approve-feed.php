<?php
	ini_set('session.cache_expire', 20160);  /* 2 weeks */
	ini_set('session.gc_maxlifetime', 1209600);
	ini_set('session.use_only_cookies', 1);
	session_name('planetoid_admin');
	session_start();
	
	if($_GET['ajax'] == 'true') {
		$ajax= true;
	}
	
	$id= $_GET['id'];
	
	if(isset($_SESSION['uid']) && $_SESSION['ulevel'] == 'admin') {
		if(isset($id)) {
			include('../inc/simplepie/idn/idna_convert.class.php');
			include('../inc/simplepie/simplepie.inc');
			include('../config.php');
			include('../planetoid.php');
			
			if($ajax) {
				include('feeds-functions.php');
			}
			
			$id= sql_escape($id);
			sql_query("UPDATE feeds SET approved=1 WHERE id='".sql_escape($id)."';");
			$curr_feed_d= sql_action("SELECT * FROM feeds WHERE id='".sql_escape($id)."';");
			$mail= $curr_feed_d['email'];
			
			$admin_mail= sql_query("SELECT email FROM users WHERE role_level='admin';");
			$admin_mail= $admin_mail['email'];
			
			if($mail != $admin_mail) {
				$mail_cont= nl2br("Your feed on <a href=\"".get_home_link()."\">".get_title()."</a> has been approved.
				---
				Powered by <a href=\"http://project-planetoid.org\">Planetoid</a> ".PLANETOID_VERSION." - Generated on ".date('r'));
				
				mail($mail, "Planetoid administration", $mail_cont, "From: Planetoid <noreplay@planetoid.services> \r\n"
				."Content-Type: text/html; charset=UTF-8\r\n"
				."X-Mailer: PHP/" . phpversion());
			}
			
			if($ajax) {
				$links= generate_manage_links($id, 1);
				$manage= $links['manage'];
				$new_note= $links['new_note'];
				$hidden= $links['hidden'];
				
				$table_row= "<td class=\"num\">$id $new_note</td><td>'+feedURL+'</td><td>$manage</td>";
				
				echo "var feedURL= $('#table-row-$id td:eq(1)').html();";
				echo "$('#table-row-$id').html('$table_row').Highlight(1000, '#82d93e');";
			} else {
				header("Location: {$_GET['r_to']}");
			};
			
			
			add_feed($curr_feed_d);
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
			echo "window.location('../login.php');";
		} else {
			header("Location: ../login.php");
		}
	}
?>