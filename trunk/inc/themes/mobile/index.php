<?php
// 	header('Cache-control: ')
/* From CodeIgniter (from text helpers) */
function character_limiter($str, $n= 500, $end_char= '&#8230;') {
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
<html><head><title><?=get_title()?> (mobile edition)</title><style>body { color:#fff;background:#0d0d08;font-size:13px; } a {color: #fffbe0 } hr { width:30%;background:#c0c0c0; }</style></head><body><h1><a href="<?=get_home_link()?>" style="color:#ffff9b"><?=get_title()?></a></h1>
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

$navigation= '<p style="text-align:center">';
	if($page > 1) {
		$navigation .= "<a href=\"index.php?p=".($page-1)."\">&laquo; Previous</a> | <a href=\"index.php?p=1\">Home</a> |";
	}
	
	if($page_end < count($articles)) {
		$navigation .= "<a href=\"index.php?p=".($page+1)."\">Next &raquo;</a>";
	}
$navigation .= '</p>';

for($n= $page_start; $n < $page_end; $n++):
	$article= $articles[$n];
	
	$link= $article['permalink'];
	$title= $article['title'];
	$author= $article['author'];
	$post_time= $article['post_time'];
	$post= character_limiter(strip_tags($article['description']), 200);
?>
<h3><a href="<?=$link?>"><?=$title?></a></h3>
<small><?=$author?> on <?=$post_time?></small>
<p><?=$post?></p><hr/><?php endfor; ?>
<?=$navigation?><a href="<?=get_home_link()?>"><?=get_title()?></a><br/>Powered by Planetoid</div></body></html>