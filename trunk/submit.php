<?php
		 
	include('config.php');
	include('planetoid.php');
	$allow_this= get_setting_value('show_reg_button');
	
	if($allow_this != 'on') {
		sql_close();
		header('Location: index.php');
		exit(0);
	};
	
	if($allow_this && $_POST['action'] == 'submit') {
		if(isset($_POST['url']) && isset($_POST['email']) && isset($_POST['pass'])) {
			if(isset($_FILES['avatar'])) {
				$avatar_flnm= basename($_FILES['avatar']['name']);
				$avatar_name= substr(md5($avatar_flnm.time()), 0, 6);
				$ext= explode('.', $avatar_flnm);
				
				$avatar= "avatars/{$avatar_name}.{$ext[1]}";
				
				if(!move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar)) {
					$avatar= 'inc/images/no-avatar.png';
				}
			} else {
				$avatar= 'inc/images/no-avatar.png';
			}
			
			sql_query("INSERT INTO feeds VALUES (".sql_autoid('feeds').", '".sql_escape($_POST['url'])."', '".sql_escape($_POST['email'])."', '$avatar', 0, '".date('Y-m-d')."');");
			
			sql_query("INSERT INTO users VALUES (".sql_autoid('users').", '".sql_escape($_POST['email'])."', '".md5($_POST['pass'])."', '".sql_escape($_POST['name'])."', 'feed_owner');");
			
			sleep(1);
			refresh_cache();
			
			if(!sql_query) {
				$error= "An error occured. Try again later.";
			} else {
				$msg= "Your submission has been saved, you will be notified about when (if) your feed will be approved.";
				$to_notifiy= get_setting_value('reg_notifiy');
				
				if($to_notifiy == 'on' ) {
					$admin_mail= sql_query("SELECT email FROM users WHERE role_level='admin';");
					$admin_mail= $admin_mail['email'];
					
					$mail_cont= nl2br("Someone has submited feed on <a href=\"".get_home_link()."\">".get_title()."</a> with following details:
					
					Feed URL: {$_POST['url']}
					Submitters email: <a href=\"mailto:{$_POST['email']}\">{$_POST['email']}</a>
					---
					Powered by <a href=\"http://planetoid-project.org\">Planetoid</a>".PLANETOID_VERSION." - Generated on ".date('r'));
					
					mail($admin_mail, "Planetoid adminstration", $mail_cont, "From: Planetoid <noreplay@planetoid.services> \r\n"
					."Content-Type: text/html; charset=UTF-8\r\n"
					."X-Mailer: PHP/" . phpversion());
				}
			}
			
// 			sql_close();
		} else {
			header("Location: {$_POST['r_to']}");
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="text/xhtml; charset=utf-8" />
		<title><?=get_title()?> &raquo; Submit feed</title>
		<link href="admin/inc/css/login-install.css" rel="stylesheet" />
		<link href="admin/inc/favicon.ico" rel="icon" />
		<link href="admin/inc/favicon.ico" rel="shortcut icon" />
	</head>
	<body>
		<form action="submit.php" method="POST" enctype="multipart/form-data" class="install">
			<a href="http://planetoid-project.org"><img src="admin/inc/images/logo-login.png" alt="Planetoid's logo" /></a>
			<?php if(isset($error)) { ?>
			<div class="error">
				<?=$error?>
			</div>
			<?php } else if(isset($msg)) { ?>
			<div class="info-info">
				<?=$msg?>
			</div>
			<?php } else {?>
			<div class="info-info">
				Submit your feed to <?=get_title()?> administrators if you want it to appear on <?=get_title()?> homepage.
			</div>
			<?php }; ?>
			<div class="info">
				Information about your feed
			</div>
			<label for="url">Feed URL:</label>
			<input type="text" name="url" id="url" value="http://" />
			
			<label for="avatar">Hackergotchi: <small>(max 0.5 MB)</small></label>
			<input type="hidden" name="MAX_FILE_SIZE" value="524288" /><!-- 0.5 mb -->
			<input type="file" name="avatar" id="avatar" />
			
			<label for="email">Where to send notifications: <small>(your email)</small></label>
			<input type="text" name="email" id="email" />
			
			<hr/>
			
			<div class="info">
				You account details
			</div>
			
			<label for="pass">Password:</label>
			<input type="password" name="pass" id="pass" />
			
			<label for="name">Name:</label>
			<input type="text" name="name" id="name" />
			
			<p>
				<input type="hidden" value="submit" name="action" />
				<input type="reset" value="Reset form" />
				<input type="submit" value="Save feed &raquo;" />
			</p>
			</form>
	</body>
</html>
<?php sql_close(); ?>
