<?php	 
	error_log(0);
	header('Content-Type: text/xml');
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	
	require_once('inc/simplepie/idn/idna_convert.class.php');
	require_once('inc/simplepie/simplepie.inc');
	require_once('config.php');
	require_once('planetoid.php');
?>
<rss version="2.0" 
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	>
	<channel>
		<title><?=get_title()?></title>
		<link><?=get_home_link()?></link>
		<description><?=get_description()?></description>
		<generator>http://planetoid-project.org/</generator>
		<?php
		$articles= list_articles();
		for($n=0; $n < count($articles); $n++):
			$article= $articles[$n];
		?>
		<item>
			<title><?=$article['title']?></title>
			<dc:creator><?=$article['author']?></dc:creator>
			<pubDate><?=$article['post_time']?></pubDate>
			<description><![CDATA[<?=$article['description']?>]]></description>
			<guid isPermaLink="true"><?=$article['permalink']?></guid>
		</item>
		<?php endfor;sql_close();?>
	</channel>
</rss>
