<?php
	include('inc/simplepie/simplepie.inc');
	include_once('inc/simplepie/idn/idna_convert.class.php');
	include('config.php');
	include('planetoid.php');
	
	header('Content-Type: text/xml');
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<!-- generator="planetoid/<?php echo PLANETOID_VERSION; ?>" -->
<rss version="2.0" 
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	>
	<channel>
		<title><?php echo get_title(); ?></title>
		<link><?php echo get_home_link(); ?></link>
		<description><?php echo get_description(); ?></description>
		<generator>http://planetoid-project.org/</generator>
		<?php
		$articles= list_articles();
		for($n=0; $n < count($articles); $n++) {
			$article= $articles[$n];
		?>
		<item>
			<title><?php echo $article['title']; ?></title>
			<dc:creator><?php echo $article['author']; ?></dc:creator>
			<pubDate><?php echo $article['post_time']; ?></pubDate>
			<description><![CDATA[<?php echo $article['description']; ?>]]></description>
			<guid isPermaLink="true"><?php echo $article['permalink']; ?></guid>
		</item>
		<?php
		};
		sql_close();
		?>
	</channel>
</rss>