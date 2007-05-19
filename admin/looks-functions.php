<?php

function make_theme_list() {
	$themes= list_info_dir('../inc/themes', 'theme');
	$current_theme= get_setting_value("theme_dir_name");
	
	for($n=0; $n < count($themes); $n++) {
		$theme= $themes[$n];
		$dirname= $theme[1];
		$attrs= $theme[0];
		$name= $attrs['Name'];
		if($dirname == $current_theme) {
			$spid= " id=\"curr-theme-box\"";
		} else {
			$spid= '';
		}
		$onclick= "onclick=\"Settings.setTheme('$dirname');return false;\"";
		echo "<div class=\"theme-icon action-link\" $onclick $spid>";
			echo "<a href=\"set-theme.php?dirname=$dirname\" title=\"{$attrs['Comment']}\" class=\"action-link\" $onclick>";
				echo "<img src=\"../inc/themes/$dirname/{$attrs['Icon']}\" alt=\"$name\" width=\"160px\" height=\"160px\" class=\"theme-image\" />";
			echo "</a>";
		
			if($dirname==$current_theme) {
				echo "<img src=\"inc/images/ok32.png\" alt=\"current theme\" title=\"Planetoid is currently using this theme.\" id=\"curr-theme\" />";
			} else {
				echo "<a href=\"set-theme.php?dirname=$dirname\" title=\"{$attrs['Comment']}\" class=\"action-link\" $onclick><img src=\"inc/images/set-theme.png\" alt=\"current theme\" title=\"Select this theme.\" class=\"set-theme\" /></a>";
			}
			
			echo "<span>$name {$attrs['Version']}<br/><small>by <a href=\"{$attrs['AuthorURL']}\" target=\"_blank\">{$attrs['Author']}</a></small></span>";
		echo "</div>";
	}
	
	echo "<br style=\"clear:both\"/>";
}

?>