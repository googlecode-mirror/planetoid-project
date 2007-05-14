<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="text/xhtml; charset=utf-8" />
		<title>Planetoid installation</title>
		<link href="admin/inc/css/login-install.css" rel="stylesheet" />
		<link href="admin/inc/favicon.ico" rel="icon" />
		<link href="admin/inc/favicon.ico" rel="shortcut icon" />
		<script type="text/javascript">function $(el) { return document.getElementById(el); };</script>
	</head>
	<body>
		<form class="install">
			<img src="admin/inc/images/logo-login.png" alt="Planetoid's logo" />
<?php
include('config.php');
	if(SQL_TYPE == 'pgsql') {
		$db_link= pg_connect('host='.SQL_HOST.' port='.SQL_PORT.' dbname='.SQL_DB_NAME.' user='.SQL_USER.' password='.SQL_PASS)
			or die('<div class="error">PostgreSQL Error: '.pg_last_error().'</div>');
	} else if(SQL_TYPE == 'mysql') {
		$db_link= mysql_connect(SQL_HOST, SQL_USER, SQL_PASS)
			or die('<div class="error">MySQL Error: '.mysql_error().'</div>');
		mysql_select_db(SQL_DB_NAME) or die('<div class="error">MySQL Error: '.mysql_error().'</div>');
	}
	
function sql_close() {
	global $db_link;
	if(SQL_TYPE == 'pgsql') {
		pg_close($db_link);
	} else if(SQL_TYPE == 'mysql') {
		mysql_close($db_link);
	}
};

function sql_action($action, $return= false) {
	if(SQL_TYPE == 'pgsql') {
		$db_q= pg_query($action);
		if($return) {
			$db_r= pg_fetch_array($db_q, NULL, PGSQL_ASSOC);
		}
	} else if(SQL_TYPE == 'mysql') {
		$db_q= mysql_query($action) or die('<div class="error">MySQL Error: '.mysql_error().'</div>');
		if($return) {
			$db_r= mysql_fetch_array($db_q, MYSQL_ASSOC);
		}
	};
	
	if($return) {
		return $db_r;
	}
};

function sql_escape($string) {
	if(SQL_TYPE == 'pgsql') {
		return pg_escape_string($string);
	} else if(SQL_TYPE == 'mysql') {
		return mysql_escape_string($string);
	}
};

function sql_autoid($table) {
	if(SQL_TYPE == 'pgsql') {
		return "nextval('{$table}_id_seq')";
	} else if(SQL_TYPE == 'mysql') {
		return 'NULL';
	}
}

if(SQL_TYPE == 'pgsql') {
	sql_action("CREATE TABLE feeds ("
		."id serial,"
		."CONSTRAINT feeds_pkey PRIMARY KEY (id),"
		."url varchar,"
		."CONSTRAINT feeds_url_key UNIQUE (url),"
		."email varchar,"
		."avatar varchar,"
		."approved int,"
		."reg_date date,"
		.");");
		
	sql_action("CREATE TABLE users ("
		."id serial,"
		."CONSTRAINT users_pkey PRIMARY KEY (id),"
		."email varchar,"
		."CONSTRAINT users_email_key UNIQUE (email),"
		."pass varchar(32),"
		."name varchar,"
		."role_level varchar);");
		
	sql_action("CREATE TABLE settings ("
		."id serial,"
		."CONSTRAINT settings_pkey PRIMARY KEY (id),"
		."name varchar,"
		."CONSTRAINT settings_name_key UNIQUE (name),"
		."value text,"
		.");");
} else if(SQL_TYPE == 'mysql') {
	sql_action('CREATE TABLE `feeds` ('
		.'`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,'
		.'`url` VARCHAR(255) NOT NULL, `email` VARCHAR(255) NOT NULL,'
		.'`avatar` VARCHAR(255) NOT NULL,'
		.'`approved` INT(1) NOT NULL,'
		.'`reg_date` DATE,'
		.'UNIQUE (`url`)) ENGINE = myisam;');
		
	sql_action('CREATE TABLE `users` ('
		.'`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,'
		.'`email` VARCHAR(255) NOT NULL,'
		.'`pass` VARCHAR(32) NOT NULL,'
		.'`name` VARCHAR(255) NOT NULL,'
		.'`role_level` VARCHAR(255) NOT NULL,'
		.'UNIQUE (`name`)) ENGINE = myisam;');
	
	sql_action('CREATE TABLE `settings` ('
		.'`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,'
		.'`name` VARCHAR(255) NOT NULL,'
		.'`value` TEXT, UNIQUE (`name`)) ENGINE = myisam;');
}
sleep(1);

sql_action("INSERT INTO feeds VALUES (".sql_autoid('feeds').", 'http://josiplisec.net/feed', '".sql_escape($_POST['u_mail'])."', 'http://josiplisec.net/files/planetoid_avatar.png', 1, '".date('Y-m-d')."');");

sql_action("INSERT INTO users VALUES (".sql_autoid('users').", '".sql_escape($_POST['u_mail'])."', '".md5(sql_escape($_POST['u_pass']))."', '".sql_escape($_POST['u_name'])."', 'admin');");

sql_action("INSERT INTO settings VALUES (".sql_autoid('settings').", 'title', '".sql_escape($_POST['title'])."');");
sql_action("INSERT INTO settings VALUES (".sql_autoid('settings').", 'description', '".sql_escape($_POST['desc'])."');");
sleep(1);

sql_action("INSERT INTO settings VALUES (".sql_autoid('settings').", 'base_url', '".sql_escape($_POST['dir'])."');");
sql_action("INSERT INTO settings VALUES (".sql_autoid('settings').", 'base_link', '".sql_escape($_POST['link'])."');");
sql_action("INSERT INTO settings VALUES (".sql_autoid('settings').", 'show_reg_button', 'on');");
sleep(1);

sql_action("INSERT INTO settings VALUES (".sql_autoid('settings').", 'reg_notify', 'on');");
sql_action("INSERT INTO settings VALUES (".sql_autoid('settings').", 'theme_dir_name', 'default');");
sql_action("INSERT INTO settings VALUES (".sql_autoid('settings').", 'posts_num', '0');");
sleep(1);

sql_action("INSERT INTO settings VALUES (".sql_autoid('settings').", 'date_format', 'j\<\s\u\p\>S\<\/\s\u\p\> M Y');");
sql_action("INSERT INTO settings VALUES (".sql_autoid('settings').", 'title_regexp', '');");
sql_action("INSERT INTO settings VALUES (".sql_autoid('settings').", 'content_regexp', '');");
sleep(1);

sql_action("INSERT INTO settings VALUES (".sql_autoid('settings').", 'install_time', '".time()."');");
sql_action("INSERT INTO settings VALUES (".sql_autoid('settings').", 'installed', 'true');");
sql_close();
?>

		<strong>Congratulations! Planetoid has been successfully installed!</strong>
		<hr/>
			Proceed to:
			<ul>
				<li><a href="<?=$_POST['link']?>"><?=$_POST['title']?> homepage</a></li>
				<li><a href="admin/">Admin pages</a></li>
			</ul>
			<hr/>
			<div class="info">
				Note: You should remove config-config.php, install.php &amp; install_db.php files
			</div>
		</form>
	</body>
</html>
