<?php include('functions.php');  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="text/xhtml; charset=utf-8" />
		
		<title><?php echo get_title(); ?></title>
		<meta name="description" content="<?php echo get_description(); ?>" />
		<meta name="generator" content="Planetoid <?php echo PLANETOID_VERSION; ?>" />
		<link href="<?php echo THEME_PATH; ?>/style.css" type="text/css" rel="stylesheet" />
		<link href="<?php echo THEME_PATH; ?>/favicon.ico" type="x-image/ico" rel="icon" />
		<link href="<?php echo THEME_PATH; ?>/favicon.ico" type="x-image/ico" rel="shortcut icon" />
		
<?php
planetoid_feed();
link_feeds(); ?>
		
	</head>
	<body>
		<div id="header">
			<h1><a href="<?php echo get_home_link(); ?>"><?php echo get_title(); ?></a></h1>
		</div>
		<div id="top-bar">
			<div class="row">
				<h5>About this planet</h5>
				<p>
					<?php echo get_description(); ?>
				</p>
			</div>
			<div class="row">
				<h5>Aggregated blogs</h5>
				<ul>
				<?php list_blogs(); ?>
				<li><a href="opml.php">OPML list of aggregated blogs</a></li>
				</ul>
			</div>
			<br style="clear:both" />
		</div>
		<div id="articles">
		<?php
		$articles= list_articles();
		for($n=0; $n < count($articles); $n++) {
			$article= $articles[$n];
			$link= $article['permalink'];
			$title= $article['title'];
			$author= $article['author'];
			$post_time= $article['post_time'];
			$post= $article['description'];
			$avatar= $article['avatar_url'];
		?>
			<div class="article">
				<div class="article-body">
					<h2><a href="<?php echo $link; ?>" target="_blank"><?php echo $title; ?></a></h2>
					<?php echo $post; ?>
					<br style="clear:both" />
				</div>
				<div class="article-head">
					<img src="<?php echo $avatar; ?>" class="avatar" alt=":P" width="50px" height="5px" />
					<h3><?php echo $author; ?></h3>
					<h4><?php echo $post_time; ?></h4>
				</div>
			</div>
			<br style="clear:both;" />
			<hr style="display: none;" />
		<?php
		};
		?>
	</div>
	<div id="footer">
		&copy; 2007 <a href="<?php echo get_home_link(); ?>"><?php echo get_title(); ?></a>
		<a href="http://planetoid-project.org">
			<?php
			if(strstr($_SERVER['HTTP_USER_AGENT'], 'Mozilla')) {
				$mozfix=" class=\"mozfix\""; /* Firefox fix */
			} ?>
			<img src="<?php echo THEME_PATH; ?>/images/poweredby-planetoid.png" alt="Powered by Planetoid" title="Powered by Planetoid"<?php echo $mozfix; ?> />
		</a>
		<br style="clear:both" />
	</div>
	</body>
</html>