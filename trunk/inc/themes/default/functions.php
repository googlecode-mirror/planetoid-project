<?php

function list_blogs() {
	$feeds= list_feeds();
	
	for($n=0; $n < count($feeds); $n++) {
		$blog= $feeds[$n];
		$feed_link= " <small><a href=\"{$blog['feedUrl']}\" class=\"feed\">Feed</a></small>";
		
		echo "<li><a href=\"{$blog['pageUrl']}\" title=\"{$blog['description']}\">{$blog['title']}</a>$feed_link</li>";
	};
};

?>