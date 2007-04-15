<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="text/xhtml; charset=utf-8" />
		<title>Planetoid installation</title>
		<link href="admin/inc/css/login-install.css" rel="stylesheet" />
		<link href="admin/inc/favicon.ico" rel="icon" />
		<link href="admin/inc/favicon.ico" rel="shortcut icon" />
	</head>
	<body>
		<form action="install_db.php" method="POST" class="install">
			<img src="admin/inc/images/logo-login.png" alt="Planetoid's logo" />
			<div class="info-info">
				<strong>Welcome to Planetoid installation!</strong><br/>
				To install Planetoid all you have to do is to fill fields below.
			</div>
			
			<hr/>
			<div class="info">
				Basic information about your planet.
			</div>
			<label for="title">Title:</label>
			<input type="text" name="title" id="title" /><br/>
			
			<label for="desc">Description:</label>
			<input type="text" name="desc" id="desc" /><br/>
			
			<label for="dir">Absolute path to Planetoid files:</label>
			<input type="text" name="dir" id="dir" value="<?php echo dirname(__FILE__); ?>" /><br/>
			
			<label for="link">Link to homepage:</label>
			<input type="text" name="link" id="link" value="http://" /><br/>
			
			<hr/>
			<div class="info">
				Login details for adminstrator account.
			</div>
			
			<label for="email">Email:</label>
			<input type="text" name="u_mail" id="email" /><br/>
			
			<label for="pass">Password:</label>
			<input type="password" name="u_pass" id="pass" /><br/>
			
			<label for="name">Name:</label>
			<input type="text" name="u_name" id="name" /><br/>
			
			<p>
				<small><a href="config-config.php">Configure config.php</a></small>
				<input type="submit" value="Install! &raquo;" />
			</p>
		</from>
	</body>
</html>
