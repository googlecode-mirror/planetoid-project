/*
Planetoid project - bringing people and communities together.
Copyright (C) 2007 Mario Đanić and Josip Lisec

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

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
