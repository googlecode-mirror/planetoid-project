<?php
/* From CodeIgniter (from text helpers) */
function character_limiter($str, $n = 500, $end_char = '&#8230;') {
	if (strlen($str) < $n) {
		return $str;
	}
		
	$str = preg_replace("/\s+/", ' ', preg_replace("/(\r\n|\r|\n)/", " ", $str));

	if (strlen($str) <= $n) {
		return $str;
	}
	
	$out = "";
	
	foreach (explode(' ', trim($str)) as $val) {
		$out .= $val.' ';
		if (strlen($out) >= $n) {
			return trim($out).$end_char;
		}
	}
};

?>
<html><head><title><?=get_title()?></title></head><body>
<h1><a href="<?=get_home_link()?>"><?=get_title()?></a> (mobile edition)</h1>
<?php
$articles= list_articles();


if($page > (count($articles) - 20)) {
	$page= 1;
}

$page_start= (($page-1) * 10);
$page_end= ($page_start + 10);
$max_posts= get_setting_value("posts_num");

if($max_posts == 0) {
	$max_posts= count($articles);
}

if($page_end > $max_posts) {
	$page_start= 0;
	$page_end= 10;
}

for($n= $page_start; $n < $page_end; $n++):
	$article= $articles[$n];
	
	$link= $article['permalink'];
	$title= $article['title'];
	$author= $article['author'];
	$post_time= $article['post_time'];
	$post= character_limiter($article['description'], 200);
?>
<h2><a href="<?=$link?>"><?=$title?></a></h2>
<p><?=$post?></p>
<br style="clear:both;"/><?=$author?> on <?=$post_time?><hr/>
<?php endfor; ?>
<?php if($page > 1): ?>
<a href="index.php?p=<?=($page-1)?>">&laquo; Previous</a> | <a href="index.php?p=1">Home</a> |
<?php endif; ?>
<?php if($page_end < count($articles)): ?>
<a href="index.php?p=<?=($page+1)?>">Next &raquo;</a>
<?php endif; ?>
<hr/>&copy; 2007 <a href="<?=get_home_link()?>"><?=get_title()?></a><br/>Powered by Planetoid</div></body></html>