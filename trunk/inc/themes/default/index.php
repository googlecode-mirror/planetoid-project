<?php include('functions.php');  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="text/xhtml; charset=utf-8" />
		
		<title><?= get_title() ?></title>
		<meta name="description" content="<?= get_description() ?>" />
		<meta name="generator" content="Planetoid <?= PLANETOID_VERSION ?>" />
		<link href="<?= THEME_PATH ?>/style.css" type="text/css" rel="stylesheet" />
		<link href="<?= THEME_PATH ?>/favicon.ico" type="x-image/ico" rel="icon" />
		<link href="<?= THEME_PATH ?>/favicon.ico" type="x-image/ico" rel="shortcut icon" />
		
<?= planetoid_feed() ?>
<?= link_feeds() ?>
	</head>
	<body>
		<div id="header">
			<h1><a href="<?= get_home_link() ?>"><?= get_title() ?></a></h1>
		</div>
		<div id="articles hfeed">
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
			<div class="article hentry">
				<div class="article-head">
					<img src="<?= $avatar ?>" class="avatar" alt=":P" width="50px" height="5px" />
					<h2><a href="<?= $link ?>" target="_blank" class="entry-title permalink" rel="bookmark"><?= $title ?></a></h2>
					<h3><?= $author ?></h3>
					<h4 class="updated published" title="<?= hAtom_date($article['timestamp']) ?>"><?= $post_time ?></h4>
				</div>
				<div class="article-body entry-content">
					<?= $post ?>
					<br style="clear:both;" />
				</div>
			</div>
			<br style="clear:both;" />
			<hr style="display: none;" />
		<?php
		};
		?>
	</div>
	<div id="sidebar">
		<h5>About this planet</h5>
		<p>
			<?= get_description() ?>
		</p>
		
		<h5>Aggregated blogs</h5>
		<ul>
			<?= list_blogs() ?>
		</ul>
	</div>
	<div id="footer">
		&copy; 2007 <a href="<?= get_home_link() ?>"><?= get_title() ?></a>
		<a href="http://planetoid-project.org">
			<?php
			if(strstr($_SERVER['HTTP_USER_AGENT'], 'Mozilla')) {
				$mozfix=" class=\"mozfix\"";
			} ?>
			<img src="<?= THEME_PATH ?>/images/poweredby-planetoid.png" alt="Powered by Planetoid" title="Powered by Planetoid"<?= $mozfix ?> />
		</a>
		<br style="clear:both" />
	</div>
	</body>
</html>
