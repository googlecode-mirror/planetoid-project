<?php

ini_set('session.cache_expire', 20160);  /* 2 weeks */
ini_set('session.gc_maxlifetime', 1209600);
ini_set('session.use_only_cookies', 1);
session_name('planetoid_admin');
session_start();


include('config.php');
include('planetoid.php');

if(isset($_SESSION['uid']) && isset($_SESSION['ulevel'])) {
	header("Location: admin/");
	exit(0);
};
	
if($_GET['lost_pass'] == 'true') {
 $lost_pass= true;
} else {
	$lost_pass= false;
}

if($_POST['restore_pass'] == 'true') {
	$current_pass= sql_action("SELECT pass FROM users WHERE email='".sql_escape($_POST['email'])."';");
	if($current_pass) {
		$new_pass= hash('md5', $current_pass['pass']);
		$new_pass= substr($new_pass, 0, 6);
// 		echo $new_pass;
		$new_pass= hash('md5', $new_pass);
		
		sql_action("UPDATE users SET pass='$new_pass' WHERE email='".sql_escape($_POST['email'])."'");
		
		$mail_cont= "Your password has been reset, the new password is: '$new_pass'."
					."<br/>You can login here: "
					."<a href=\"".get_home_link()."\">".get_home_link()."</a>"
					."<br/>---</br/>Powered by <a href=\"http://planetoid-project.org\">Planetoid</a>";
		
		mail($_POST['email'], "Planetoid adminstration", $mail_cont, "From: Planetoid <noreplay@planetoid.services> \r\n"
			."Content-Type: text/html; charset=UTF-8\r\n"
			."X-Mailer: PHP/" . phpversion());
		
		$error= "New password has been sent to your email.";
	} else {
		$error= 'There is no user with this email, try again.';
	}
}

if(isset($_POST['email']) && isset($_POST['pass']) && (strlen($_POST['email']) + strlen($_POST['email'])) != 0 && $_POST['action'] == 'login') {
	
	$user_props= sql_action("SELECT * FROM users WHERE email='".sql_escape($_POST['email'])."' AND pass='".md5(sql_escape($_POST['pass']))."' AND role_level='admin';");
	
	if(isset($user_props['id'])) { /* Just a check if SQL query did retrun any result */
		$_SESSION['uid']= $user_props['id'];
		$_SESSION['ulevel']= $user_props['role_level'];
		
		header('Location: admin/');
	} else {
		$error= 'Wrong email or password.';
	};
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="text/xhtml; charset=utf-8" />
		<title><?php echo get_title(); ?> &raquo; Login</title>
		<link href="admin/inc/css/login-install.css" rel="stylesheet" />
		<link href="admin/inc/favicon.css" rel="icon" />
		<link href="admin/inc/favicon.css" rel="shortcut icon" />
	</head>
	<body>
		<form action="login.php" method="POST" class="login">
			<img src="admin/inc/images/logo-login.png" alt="Planetoid's logo" />
			<h1><?php echo get_title(); ?> &raquo; Login</h1>
			<?php if(isset($error)) { ?>
			<div class="error" onclick="this.style.display='none';">
				<?php echo $error; ?>
			</div>
			<?php }; ?>
			<label for="email">Email:</label>
			<input type="text" name="email" id="email" value="<?php if(isset($_POST['email'])) { echo $_POST['email']; }; ?>" />
			<?php if(!$lost_pass) { ?>
			<br/>
			<label for="pass">Password:</label>
			<input type="password" name="pass" id="pass" value="<?php if(isset($_POST['pass'])) { echo $_POST['pass']; }; ?>" />
			<input type="hidden" name="action" value="login" />
			<?php }; ?>
			<p style="text-align:right;">
				<?php if($lost_pass) {?><input type="hidden" name="restore_pass" value="true" /><?php }; ?>
				<?php if(!$lost_pass) { ?><a href="login.php?lost_pass=true">Lost password?</a><? }; ?>
				<input type="submit" value="<?php if(!$lost_pass) { ?>Login<?php } else { ?>Submit<?php }; ?> &raquo;" />
			</p>
		</form>
	</body>
</html>
<?php sql_close(); ?>
