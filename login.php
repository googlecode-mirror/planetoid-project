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
			<br/>
			<label for="pass">Password:</label>
			<input type="password" name="pass" id="pass" value="<?php if(isset($_POST['pass'])) { echo $_POST['pass']; }; ?>" />
			<input type="hidden" name="action" value="login" />
			<p>
				<input type="submit" value="Login &raquo;" />
			</p>
		</form>
	</body>
</html>
<?php sql_close(); ?>