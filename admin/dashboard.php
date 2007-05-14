<?php

ini_set('session.cache_expire', 20160);
ini_set('session.gc_maxlifetime', 1209600);
ini_set('session.use_only_cookies', 1);
session_name('planetoid_admin');
session_start();

if(isset($_SESSION['uid']) && $_SESSION['ulevel'] == 'admin') {
	require('../config.php');
	require('../planetoid.php');
	$curr_page= basename(__FILE__);
	include('header.php');
?>
		<div id="page">
			<div id="page-area">
				<h2>Dashboard</h2>
				<?php/*<div class="info">
					<strong>This is hello note</strong>
					Note: ask Mario to write it :)
				</div>*/?>
				<?php if($_SESSION['ulevel'] == 'admin'):
					$unapproved= 0;
					
					for($n=0; $n < count($feeds_d); $n++) {
						$feed= $feeds_d[$n];
						if($feed['approved'] == 0) {
							$unapproved++;
							$unapproved_feeds .= "<a href=\"edit-feed.php?id={$feed['id']}\">{$feed['url']}</a><br/>";
						};
					};
					
					if($unapproved != 0):
					?>
						<h3>New requests</h3>
						<p class="settings">
							<?=$unapproved_feeds?>
						</p>
						<br/>
					<?php endif;endif; ?>
					<h3>Planetoid Blog</h3>
					<div class="info">We don't have a blog yet :)</div>
				</div>
		</div>
<?php
	include('footer.php');
	sql_close();
} else {
	header("Location: ../login.php");
}
?>
