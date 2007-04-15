<?php

function list_blogs() {
	$feeds= list_feeds();
	
	for($n=0; $n < count($feeds); $n++) {
		$blog= $feeds[$n];
		$feed_link= " <a href=\"{$blog['feedUrl']}\" class=\"feed-link\">&#8594;Feed</a>";
		
		echo "<li><a href=\"{$blog['pageUrl']}\" title=\"{$blog['description']}\">{$blog['title']}</a>$feed_link</li>";
		if(($n % 4) == 0 && $n != 0) {
			echo "</ul></div><div class=\"row\"><ul>";
		}
	};
};
?>