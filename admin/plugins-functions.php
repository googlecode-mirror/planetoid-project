<?php

function make_plugins_table() {
	$plugins= list_info_dir('../inc/plugins', 'plugin');
	
	
	echo "<table cellpadding=\"0\" cellspacing=\"1px\" id=\"plugins-table\">";
		echo "<thead><tr class=\"header\"><td>Name</td><td>Description</td><td>Manage</td></tr></thead>";
		echo "<tbody>";
	
	for($n=0; $n < count($plugins); $n++) {
		$plugin= $plugins[$n];
		$dir= $plugin[1];
		$attrs= $plugin[0];
		
		echo "<tr id=\"{$dir}-row\"><td>"
			."<img src=\"../inc/plugins/{$dir}/{$attrs['Icon']}\" alt=\"\" style=\"height:16px;\" />"
			."<a href=\"{$attrs['PluginURL']}\">{$attrs['PluginName']}</a> <small>{$attrs['PluginVersion']}</small></td>"
			."<td>{$attrs['PluginDescrip']} <em>&#8210; <a href=\"{$attrs['AuthorURL']}\">{$attrs['AuthorName']}</a></em></td>"
			."<td>".generate_manage_links($dir)."</td></tr>";
	}
	
		echo "</tbody>";
	echo "</table>";
};

function generate_manage_links($dir) {
	global $curr_page;
	
	if(is_plugin_active($dir)) {
		$html= "<a href=\"plugin-settings.php?dir={$dir}&r_to={$curr_page}\">Settings</a> ";
// 		$html.= "<a href=\"plugin-checkversion.php?dir={$dir}&r_to={$curr_page}\" class=\"action-link\" onclick=\"Plugin.deactivate('{$dir}');return false;\">Check for new version</a>";
		$html.= "<a href=\"deactivate-plugin.php?dir={$dir}&r_to={$curr_page}\" class=\"action-link link-red\" onclick=\"Plugin.deactivate('{$dir}');return false;\">Deactivate</a> ";
	} else {
		$html= "<a href=\"activate-plugin.php?dir={$dir}&r_to={$curr_page}\" class=\"action-link link-green\" onclick=\"Plugin.activate('{$dir}');return false;\">Activate</a>";
	}
	
	return $html;
}

?>