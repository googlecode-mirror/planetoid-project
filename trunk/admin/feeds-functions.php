<?php

function make_feed_table() {
	global $feeds_d;
	global $curr_page;
	echo "<table cellpadding=\"0\" cellspacing=\"0\" id=\"feeds-table\">";
	echo "<thead><tr class=\"header\"><th class=\"num\"><input type=\"checkbox\" id=\"check-all\" /></th><th>URL</th><td style=\"width:150px;\">Manage</td></tr></thead>";
	echo "<tbody>";
	for($n=0; $n < count($feeds_d); $n++) {
		$feed= $feeds_d[$n];
		$id= $feed['id'];
		
		$links= generate_manage_links($id, $feed['approved']);
		$manage= $links['manage'];
		$new_note= $links['new_note'];
		$hidden= $links['hidden'];
		$avatar= $feed['avatar'];
		
		if(strlen($new_note) != 0) {
			$new_class= ' new';
		} else {
			$new_class= '';
		}
		
		echo "<tr{$hidden} id=\"table-row-{$id}\">"
						."<td class=\"num\">"
							."<input type=\"checkbox\"/>{$new_note}"
						."</td>"
						."<td>"
							."<img src=\"../$avatar\" height=\"15px\" />"
							."<a href=\"{$feed['url']}\" target=\"_blank\">{$feed['url']}</a>"
						."</td>"
						."<td class=\"manage-links\">$manage</td>"
					."</tr>\n";
	}
	
	echo "</tbody></table>";
	echo "<script type=\"text/javascript\">
		$('#feeds-table').tableSorter({
			sortColumn: 'URL',
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
		$manage = feed_edit_link($id).' ';
		$manage .= feed_hide_link($id).' ';
		$manage .= feed_remove_link($id);
		$hidden = $new_note = '';
	} else if($status == 0) {
		$manage = feed_edit_link($id).' ';
		$manage .= feed_approve_link($id).' ';
		$manage .= feed_remove_link($id, true);
		$hidden = '';
		$new_note = '<img src="inc/images/new-indicator.png" alt="*" title="Waiting to be approved" /> ';
	} else if($status == 2) {
		$manage = feed_hide_link($id, true);
		$hidden= " class=\"hidden\"";
		$new_note= '';
	}
	
	return array('manage' => $manage, 'hidden' => $hidden, 'new_note' => $new_note);
}

function icon($name, $alt = '') {
	return "<img src=\"inc/images/{$name}.png\" class=\"icon\" alt=\"$alt\" />";
}

function feed_approve_link($id) {
	global $curr_page;
	return	"<a href=\"approve-feed.php?id={$id}&amp;r_to={$curr_page}\" "
					."class=\"action-link\" "
					."onclick=\"Feeds.approve({$id});return false;\" title=\"Approve this feed\">".icon('ok16')."<span>Approve</span></a> ";
}

function feed_remove_link($id, $reject = false) {
	global $curr_page;
	if($reject) {
		$text = 'Reject';
		$icon = 'reject';
		$act = 'remove';
	} else {
		$text = 'Delete';
		$icon = 'remove';
		$act = 'remove';
	}
	
	return "<a href=\"{$act}-feed.php?id={$id}&amp;r_to={$curr_page}\""
				."class=\"action-link\" "
				."onclick=\"Feeds.{$act}({$id});return false;\" title=\"{$text} this feed\">".icon("feed-{$icon}")."<span>{$text}</span></a>";
}

function feed_hide_link($id, $unhide = false) {
	global $curr_page;
	
	if($unhide) {
		$text = 'Unhide';
		$ic = 'un';
	} else {
		$text = 'Hide';
		$ic = '';
	}
	
	return "<a href=\"hide-feed.php?id={$id}&amp;r_to={$curr_page}&amp;n_to=a\" "
				."class=\"action-link\" "
				."onclick=\"Feeds.hide({$id});return false;\" title=\"{$text} this feed\">".icon("feed-{$ic}hide")."<span>{$text}</span></a>";
}

function feed_edit_link($id) {
	global $curr_page;
	return "<a href=\"edit-feed.php?id={$id}&amp;r_to={$curr_page}\" title=\"Edit details about this feed\">".icon('feed-edit')."<span>Edit</span></a>";
}

?>