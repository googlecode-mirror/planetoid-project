<?php
	ignore_user_abort(true);
	set_time_limit(0);
	
	require_once('inc/simplepie/idn/idna_convert.class.php');
	require_once('inc/simplepie/simplepie.inc');
	require_once('config.php');
	include('planetoid.php');
	
	if(get_setting_value('pcron') == 'true') {
		if((time() - last_refresh()) > 5400) {
			$doing= false;
		} else {
			$doing= true;
		}
	} else {
		$doing= false;
	}
	
	if($_GET['force'] == true) {
		$doing= false;
	}
	
	/* No point of leaving connection open for 1 hour. */
	sql_close();
	
	if(isset($_GET['to'])) {
		$redirect_to= $_GET['to'];
	} else {
		$redirect_to= './';
	}
	
	if(isset($_GET['force_rdr'])) {
		if($_GET['force_rdr'] == 'true') {
			header("Location: {$redirect_to}");
		}
	}
	
	if(!$doing) {
		do {
			$start_caching= time();
			
			if(SQL_TYPE == 'pgsql') {
				$db_link= pg_connect('host='.SQL_HOST.' port='.SQL_PORT.' dbname='.SQL_DB_NAME.' user='.SQL_USER.' password='.SQL_PASS)
					or die('Could not connect: ' . pg_last_error());
			} else if(SQL_TYPE == 'mysql') {
				$db_link= mysql_connect(SQL_HOST, SQL_USER, SQL_PASS)
					or die('Could not connect: ' . mysql_error());
				mysql_select_db(SQL_DB_NAME) or die('Fatal error: Failed to open connection to MySQL!<br/>Check your configuration');
			}
			
			sql_query("INSERT INTO settings VALUES(".sql_autoid('settings').", 'pcron', 'true');");
			
			$feeds= array();
			$feeds_d= array();
			
			$feeds_q= sql_get_array("SELECT * FROM feeds;");
			for($n=0; $n < count($feeds_q); $n++) {
				$feed= $feeds_q[$n];
				
				if($feed['approved'] == 1) {
					$feeds[]= $feed['url'];
				}
				
				$feeds_d[]= $feed;
			};
	
			cache(serialize($feeds), $feeds_ch);
			cache(serialize($feeds_d), $feeds_d_ch);
			sleep(1);
			
			refresh_cache(false);
			sql_close();
			
			log_cache_refresh($start_caching, $end_caching);
			
			/* We have been working hard, we should sleep now :)  */
			if(!isset($_GET['force'])) {
				sleep(3600);
			} else {
				die();
			}
		} while(true);
	}
?>
<html>
	<head>
		<title>Planetoid cron</title>
		<style>
			body {padding:50px;background:#fff;font-size:1em;}
			div {width:400px;margin:auto;padding:10px;border:1px solid #c0c0c0;}
			h1 {color: #900;font-weight:normal;margin:0;padding:0;font-size:1.4em}
			p {padding: 5px;color:#000;font-size:0.9em}
			a {color: #009}
		</style>
	</head>
	<body>
		<div>
			<h1>Warning: Planetoid cron</h1>
			<p>
				You should never again open this file! Go <a href="<?=$redirect_to?>">back to site</a>.<br/>
				<small>BTW: Planetoid-cron
				<?php if(!$doing) { ?>
					has been started now.
				<?php } else { ?>
					is already running, last refresh was on <?=last_refresh('r')?>.
				<?php } ?></small>
			</p>
		</div>
	</body>
</html>