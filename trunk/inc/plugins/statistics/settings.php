<?php
/* This is file which will be loaded when user */
/* clicks on "Settings" link in Planetoid admin. */
/* You can use this to... well for Plugin settings :) */
?>

<h3>Visit logs</h3>
<p class="settings">
	Total visits: <?=get_plugin_setting('statistics', 'visits')?><br/>
	Since: <?=date("d.m.Y. H:i", get_plugin_setting('statistics', 'start_logging'))?>
</p>