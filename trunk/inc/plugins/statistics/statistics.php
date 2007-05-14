<?php

function log_visit() {
	$current= intval(get_plugin_setting('statistics', 'visits'));
	update_plugin_setting('statistics', 'visits', $current+1);
}

plugin_attach('footer', 'log_visit');

?>