<?php

function make_feed_table() {
	global $feeds_d;
	global $curr_page;
	echo "<table cellpadding=\"0\" cellspacing=\"1px\" id=\"feeds-table\">";
	echo "<thead><tr class=\"header\"><th class=\"num\">ID</th><th>URL</th><td>Manage</td></tr></thead>";
	echo "<tbody>";
	for($n=0; $n < count($feeds_d); $n++) {
		$feed= $feeds_d[$n];
		$id= $feed['id'];
		
		$links= generate_manage_links($id, $feed['approved']);
		$manage= $links['manage'];
		$new_note= $links['new_note'];
		$hidden= $links['hidden'];
		$avatar= $feed['avatar'];
		
		echo "<tr$hidden id=\"table-row-$id\"><td class=\"num\">$id $new_note</td><td><img src=\"../$avatar\" height=\"15px\" /> <a href=\"{$feed['url']}\" target=\"_blank\">{$feed['url']}</a></td><td>$manage</td></tr>\n";
	}
	echo "</tbody>";
	echo "</table>";
	echo "<script type=\"text/javascript\">
		$('#feeds-table').tableSorter({
			sortColumn: 'id',
			stripingRowClass: ['even','odd'],
			stripRowsOnStartUp: true,
			sortClassAsc: 'sortAsc',
			sortClassDesc: 'sortDesc',
			headerClass: 'sortMisc'
		});
	</script>";
};

function generate_manage_links($id, $status) {
	global $curr_page;
	if($status == 1) {
		$manage= "<a href=\"edit-feed.php?id=$id&amp;r_to=$curr_page\">Edit</a> ";
		$manage.= "<a href=\"hide-feed.php?id=$id&amp;r_to=$curr_page\" class=\"action-link\" onclick=\"Feeds.hide($id);return false;\">Hide</a> ";
		$manage.= "<a href=\"remove-feed.php?id=$id&amp;r_to=$curr_page\" class=\"link-red action-link\" onclick=\"Feeds.remove($id);return false;\">Delete</a>";
		$hidden='';
		$new_note='';
	} else if($status == 0) {
		$manage= "<a href=\"edit-feed.php?id=$id&amp;r_to=$curr_page\">Edit</a> ";
		$manage.= "<a href=\"approve-feed.php?id=$id&amp;r_to=$curr_page\" class=\"link-green action-link\" onclick=\"Feeds.approve($id);return false;\">Approve</a> ";
		$manage.= "<a href=\"remove-feed.php?id=$id&amp;r_to=$curr_page\" class=\"link-red action-link\" onclick=\"Feeds.remove($id);return false;\">Reject</a>";
		$hidden='';
		$new_note='&#8226;';
	} else if($status == 2) {
		$manage= "<a href=\"hide-feed.php?id=$id&amp;r_to=$curr_page\" class=\"link-unhide action-link\" onclick=\"Feeds.hide($id);return false;\">Unhide</a>";
		$hidden= " class=\"hidden\"";
		$new_note='';
	}
	
	return array('manage' => $manage, 'hidden' => $hidden, 'new_note' => $new_note);
}

?>