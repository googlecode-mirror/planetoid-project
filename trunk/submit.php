<?php
	
	require_once('inc/simplepie/idn/idna_convert.class.php');
	require_once('inc/simplepie/simplepie.inc');
	require_once('config.php');
	require_once('planetoid.php');
	$allow_this= get_setting_value('show_reg_button');
	
	if($allow_this != 'on') {
		sql_close();
		header('Location: index.php?reg=closed');
		exit(0);
	};
	
	if($allow_this == 'on' && $_POST['action'] == 'submit') {
		if(strlen($_POST['url']) >= 5 && strlen($_POST['email']) >= 5 && strlen($_POST['pass']) > 5) {
			if($_POST['gravatar'] != 'on') {
				if(isset($_FILES['avatar'])) {
					$avatar_flnm= basename($_FILES['avatar']['name']);
					$avatar_name= substr(bin2hex(md5($avatar_flnm.time(), true)), 0, 6);
					$ext= explode('.', $avatar_flnm);
					
					$avatar= "avatars/{$avatar_name}.{$ext[1]}";
					
					if(!move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar)) {
						$avatar= 'inc/images/no-avatar.png';
					}
				} else {
					$avatar= 'inc/images/no-avatar.png';
				}
			} else {
				$avatar= "http://www.gravatar.com/avatar.php?size=50&amp;gravatar_id=".bin2hex(md5(trim($_POST['email']), true));
			}
			
			sql_query("INSERT INTO feeds VALUES ("
				.sql_autoid('feeds').","
				."'".sql_escape($_POST['url'])."',"
				."'".sql_escape($_POST['email'])."',"
				."'$avatar',"
				."0,"
				."'".date('Y-m-d')."');");
			
			sql_query("INSERT INTO users VALUES ("
				.sql_autoid('users').","
				."'".sql_escape($_POST['email'])."',"
				."'".md5($_POST['pass'])."',"
				."'".sql_escape($_POST['name'])."',"
				."'feed_owner');");
			
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
		<script src="admin/inc/js/jquery-latest.pack.js" type="text/javascript"></script>
		<script src="admin/inc/js/interface.js" type="text/javascript"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$('#help a').click(function() {
					$('#url').val($(this).attr('title')).removeAttr('disabled');
				});
				
				$('#show-help').click(function() {
					$('#help').show();
					$('#url').attr('disabled', true);
					setTimeout(function() {
						$(document).one('click', function() {
							$('#help').hide();$('#url').removeAttr('disabled');
						});
					}, 100);
				});
				
				$('#gravatar').click(function() {
					if($(this).attr('checked') == true) {
						$('#hackergotchi').SlideOutUp(250);
					} else {
						$('#hackergotchi').SlideInUp(250);
					}
				});
			});
		</script>
	</head>
	<body>
		<form action="submit.php" method="post" enctype="multipart/form-data" class="install">
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
			<label for="url">Feed URL (<a href="#" id="show-help">?</a>):</label>
			<div id="help" class="info-info" style="display: none; position: absolute;">
				<a href="#" title="http://[name].wordpress.com/feed">WordPress.com</a><br/>
				<a href="#" title="http://[name].blogger.com/feeds/posts/default">Blogger.com</a><br/>
				<a href="#" title="http://community.livejournal.com/[name]/data/atom">LiveJournal.com</a><br/>
				<a href="#" title="http://[name].spaces.live.com/feed.rss">Windows Live Spaces</a><br/>
				<a href="#" title="http://www.flickr.com/photos/[name]">Flick Photos</a><br/>
				<a href="#" title="http://del.icio.us/rss/[name]">del.icio.us links</a><br/>
				<a href="#" title="http://www.xanga.com/[name]/rss">Xanga</a><br/>
			</div>
			<input type="text" name="url" id="url" value="http://" />
			
<!--			<select>
				<option>Gravatar for below given email</option>
				<option>URL</option>
				<option>Upload hackergotchi</option>
			</select>-->
			<label for="gravatar"><input type="checkbox" name="gravatar" id="gravatar" checked="true" />Use Gravatar for below given email</label>
			
			<div id="hackergotchi" style="margin:0;padding:0;display:none;">
				<label for="avatar">Upload your own hackergotchi: <small>(max 0.5 MB)</small></label>
				<input type="hidden" name="MAX_FILE_SIZE" value="524288" /><!-- 0.5 mb -->
				<input type="file" name="avatar" id="avatar" />
			</div>
			
			<hr/>
			
			<div class="info">Your account details</div>
			
			<label for="email">Email:</label>
			<input type="text" name="email" id="email" />
			
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
