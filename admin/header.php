<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="text/xhtml; charset=utf-8" />
		<?php $curr_page_title= explode('.php', $curr_page); $curr_page_title= str_replace('-', ' ', $curr_page_title[0]); ?>
		<title><?=get_title()?> Administration &raquo; <?=ucfirst(strtolower($curr_page_title))?></title>
		<link href="inc/css/admin.css" rel="stylesheet" type="text/css" />
		<link href="favicon.ico" rel="icon" type="image/ico" />
		<link href="favicon.ico" rel="shortcut icon" type="image/ico" />
		<script type="text/javascript" src="inc/js/jquery-latest.pack.js"></script>
		<script type="text/javascript" src="inc/js/jquery.tablesorter.js"></script>
		<script type="text/javascript" src="inc/js/interface.js"></script>
		<script type="text/javascript" src="inc/js/planetoid.js"></script>
	</head>
	<body>
		<div id="header">
			<h1><a href="<?=get_home_link()?>" title="Go to homepage"><?=get_title()?></a> Administration</h1>
		</div>
		<div id="menu">
			<ul>
				<?php
				if($_SESSION['ulevel'] == 'admin') {
					$menu_items= array('Dashboard', 'Feeds', 'Looks', 'Plugins', 'Planet');
				} else {
					$menu_items= array('Dashboard', 'Feed', 'Account');
				}
				
				for($n=0; $n < count($menu_items); $n++) {
					$item= $menu_items[$n];
					$link= strtolower($item).'.php';
					$class='';
					
					if($curr_page == $link || strstr($curr_page, str_replace('s', '', $link))) {
						$class= ' class="current"';
					};
					?>
				<li<?=$class?>><a href="<?=$link?>"><img src="inc/images/<?=strtolower($item)?>.png" class="icon" alt="<?$item?>" /><?=$item?></a></li>
				<?php }; ?>
			</ul>
		</div>
