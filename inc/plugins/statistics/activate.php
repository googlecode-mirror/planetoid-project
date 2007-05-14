<?php
/* This file executes when plugin is first time activated. */
/* You can use this to build tables for your plugin, or */
/* some other kind of preparations :) */
/* NOTE: Please do NOT echo() anyting  */

plugin_prepare_db(array(
	'visits' => 0,
	'start_logging' => time()
), 'statistics');

?>