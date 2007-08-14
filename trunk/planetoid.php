<?php
define('PLANETOID_VERSION', '0.1');
define('PLANETOID_REVISION', '0');
define('CACHE_DIR', dirname(__FILE__).'/cache');
define('DEBUG', false);
define('P_DB_ERROR', 44);
define('P_DB_QUERY_ERROR', 91);

// error_reporting(E_ALL);

if(SQL_TYPE == 'pgsql') {
	$db_link= pg_connect('host='.SQL_HOST.' port='.SQL_PORT.' dbname='.SQL_DB_NAME.' user='.SQL_USER.' password='.SQL_PASS)
		or eroo(P_DB_ERROR, 'Unable to connect to database server', __FILE__, __LINE__);
} else if(SQL_TYPE == 'mysql') {
	$db_link= mysql_connect(SQL_HOST, SQL_USER, SQL_PASS)
		or eroo(P_DB_ERROR, 'Unable to connect to database server', __FILE__, __LINE__);
	mysql_select_db(SQL_DB_NAME) or eroo(E_ERROR, mysql_error(), __FILE__, __LINE__);
}

define('BASE_DIR', get_setting_value('base_url'));

$_PLUGINS= array();

$feeds= array();
$feeds_d= array();
$feeds_ch= CACHE_DIR.'/feeds_base.spc';
$feeds_d_ch= CACHE_DIR.'/feeds_details.spc';


// if(!are_feeds_cached()) {
	$feeds_q= sql_get_array("SELECT * FROM feeds;");
	for($n=0; $n < count($feeds_q); $n++) {
		$feed= $feeds_q[$n];

		if($feed['approved'] == 1) {
			$feeds[]= $feed['url'];
		}

		$feeds_d[]= $feed;
	}

// 	cache($feeds, $feeds_ch);
// 	cache($feeds_d, $feeds_d_ch);
// } else {
// 	$feeds= get_cache($feeds_ch);
// 	$feeds_d= get_cache($feeds_d_ch);
// }

/* Feed functions */
function list_articles($build=false) {
	$list_articles_ch= CACHE_DIR.'/list_articles.spc';

	if(is_cached($list_articles_ch)) {
		return get_cache($list_articles_ch);
	} else {
		global $feeds;
		$feed= array(count($feeds));

		$articles= array();
// 		$date_format= sql_escape("date_format");
		for($n=0; $n < count($feeds); $n++) {
			$feed[$n]= new SimplePie();
			$feed[$n]->strip_ads(true);
			$feed[$n]->remove_div();

			$feed[$n]->feed_url($feeds[$n]);
			$feed[$n]->init();
			$link= $feed[$n]->get_feed_link();

			$avatar= sql_action("SELECT avatar FROM feeds WHERE url='$feeds[$n]';");
			$avatar= $avatar['avatar'];

			if($feed[$n]->data) {
				foreach($feed[$n]->get_items() as $item) {
					$key= $item->get_date('U');
					$author_q= $item->get_author();
					$match= false;

					if($author_q) {
						$author= $author_q->get_name();
					} else {
						$author= $link;
					}

					$title= $item->get_title();
					$content= $item->get_description();

					$title_regexp= get_setting_value('title_regexp');
					$content_regexp= get_setting_value('content_regexp');

					if(strlen($title_regexp) == 0 && strlen($content_regexp) == 0) {
						$match= true;
					} else {
						if(strlen($title_regexp) != 0) {
							$title_match= preg_match($title_regexp, $title);
						} else {
							$title_match= false;
						}

						if(strlen($content_regexp) != 0) {
							$content_match= preg_match($content_regexp, $content);
						} else {
							$content_match= false;
						}

						if($title_match || $content_match) {
							$match= true;
						}
					}

					if($match) {
						$articles[$key]= array(
							'title' => $item->get_title(),
							'author' => $author,
							'permalink' => htmlspecialchars($item->get_permalink()),
							'description' => $content,
							'timestamp' => $key,
							'post_time' => $item->get_date("j\\<\\s\\u\\p\\>S\\<\\/\\s\\u\\p\\> M Y"),
							'avatar_url' => $avatar,
						);

						/* Call plugins that are attached to "article" */
						$articles[$key]['plugin_data']= checkpoint('article', $articles[$key]);
					}
				}
			}
		}

		/* Articles are stored in $articles with their publish timestamp as key */
		/* so that they can be sorted by post time (ksort()). */
		/* Then the keys is replaced with numbers (0 - articles length)  */
		/* And splice()d to length defined in settings */

		ksort($articles);
		$articles= array_reverse($articles, false);

		$article_limit= intval(get_setting_value('posts_num'));

		if($article_limit != 0) {
			array_splice($articles, $article_limit);
		}

		cache($articles, $list_articles_ch);
		return $articles;
	}
}

function list_feeds() {
	$list_feeds_ch= CACHE_DIR.'/list_feeds.spc';

// 	if(is_cached($list_feeds_ch)) {
// 		return get_cache($list_feeds_ch);
// 	} else {
		global $feeds;
		$feed= array(count($feeds));
		$list= array();

		for($n=0; $n < count($feeds); $n++) {
			$feed[$n]= new SimplePie();
			$feed[$n]->strip_ads(true);
			$feed[$n]->feed_url($feeds[$n]);
			$feed[$n]->init();

			if($feed[$n]->data) {
				$list[]= array(
					'feedUrl' => $feeds[$n],
					'pageUrl' => $feed[$n]->get_feed_link(),
					'title' => $feed[$n]->get_feed_title(),
					'description' => $feed[$n]->get_feed_description(),
					'type' => strtolower($feed[$n]->get_type())
				);
			}
		}

// 		cache($list, $list_feeds_ch);
		return $list;
// 	}
}

function remove_feed($id) {
	global $feeds, $feeds_d;

	for($n=0; $n < count($feeds_d); $n++) {
		if($feeds_d[$n]['id'] == $id) {
			unset($feeds_d[$n]);
			unset($feeds[$n]);
			break;
		}
	}

	return true;
}

function add_feed($feed_d) {
	global $feeds, $feeds_d;

	$feeds_d[]= $feed_d;
	$feeds[]= $feed_d['url'];

	return true;
}

/* Caching functions */
function is_cached($path) {
	if(file_exists($path)) {
		$file= fopen($path, 'rw');
		$maketime= filemtime($path);
		$fileage= time() - $maketime;
		fclose($file);

		if($fileage > 3600) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function are_feeds_cached() {
	global $feeds_ch, $feeds_d_ch;

	if(is_cached($feeds_ch) == true && is_cached($feeds_d_ch) == true) {
		return true;
	} else {
		return false;
	}
}

function cache($content, $path) {
	$file = fopen($path, 'a+');
	fwrite($file, serialize($content));
	sleep(1);
	fclose($file);
}

function get_cache($path) {
	if(file_exists($path)) {
		$file = fopen($path, 'r');
		$cache = fread($file, filesize($path));
		fclose($file);

		return unserialize($cache);
	} else {
		return false;
	}
}

function refresh_cache($log=true) {
// 	$cache_files = array('list_articles');

// 	for($n=0; $n < count($cache_files); $n++) {
// 		$file = CACHE_DIR.'/'.$cache_files[$n].'.spc';

		if(file_exists(CACHE_DIR.'/list_articles.spc')) {
//			unlink($file);
			touch($file, (time()-44444));
		}
// 	}

	sleep(1);
	$start = time();

// 	$feeds = list_feeds();
	$articles = list_articles();

	if($log) {
		log_cache_refresh($start, time());
	}

	return true;
}

function last_refresh($date_format = false) {
	$log_f= fopen(CACHE_DIR.'/cron.log', 'r');
	$log= fread($log_f, filesize(CACHE_DIR.'/cron.log'));
	fclose($log_f);
	$log= explode("\n", $log);
	$last_refresh= $log[(count($log) - 2)];
	$last_refresh= explode("|", $last_refresh);

	if(!$date_format) {
		return $last_refresh[0];
	} else {
		return date($date_format, $last_refresh[0]);
	}
}

function log_cache_refresh($start, $end) {
	$cache_file= fopen(dirname(__FILE__).'/cache/cron.log', 'a+');
	$log= date('U', $end)."|".($end - $start)."\n";
	if(fwrite($cache_file, $log)) {
		sleep(1);
		fclose($cache_file);
	}
}

/* SQL functions */
function sql_close() {
	global $db_link;

	if(SQL_TYPE == 'pgsql') {
		pg_close($db_link);
	} else if(SQL_TYPE == 'mysql') {
		mysql_close($db_link);
	}
}

function sql_action($action) {
	if(SQL_TYPE == 'pgsql') {
		$db_q= pg_query($action);
		$db_r= pg_fetch_array($db_q, NULL, PGSQL_ASSOC);
	} else if(SQL_TYPE == 'mysql') {
		$db_q= mysql_query($action) or die('Error:'.mysql_error());
		$db_r= mysql_fetch_array($db_q, MYSQL_ASSOC);
	}

	return $db_r;
}

function sql_query($action) {
	if(SQL_TYPE == 'pgsql') {
		return pg_query($action) or eroo(P_DB_QUERY_ERROR, 'Unable to execute database query', __FILE__, __LINE__);
	} else if(SQL_TYPE == 'mysql') {
		return mysql_query($action) or eroo(P_DB_QUERY_ERROR, 'Unable to execute database query', __FILE__, __LINE__);
	}
}

function sql_insert($table, $data) {
	$query_fields = '';
	$query_values = '';

	if(!is_array($data)) {
		$data= array($data);
	}

	while(list($name, $val) = each($data)) {
		$query_fields .= "{$name},";
		$query_values .= "'".sql_escape($val)."',";
	}

	$query_fields = substr($query_fields, 0, -1);
	$query_values = substr($query_values, 0, -1);

	$query = "INSERT INTO {$table} ({$query_fields}) VALUES ({$query_values});";

	if(DEBUG) {
		echo $query;
	} else {
		return sql_query($query);
	}
}

function sql_escape($string) {
	if(SQL_TYPE == 'pgsql') {
		return pg_escape_string($string);
	} else if(SQL_TYPE == 'mysql') {
		return mysql_escape_string($string);
	}
}

function sql_get_array($query) {
	$resultset= array();

	if(SQL_TYPE == 'pgsql') {
		$query= pg_query($query);
		while($row = pg_fetch_array($query, NULL, PGSQL_ASSOC)) {
			$resultset[]= $row;
		}
	} else {
		$query= mysql_query($query);
		while($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
			$resultset[]= $row;
		}
	}

	return $resultset;
}

function sql_autoid($table) {
	if(SQL_TYPE == 'pgsql') {
		return "nextval('{$table}_id_seq')";
	} else if(SQL_TYPE == 'mysql') {
		return 'NULL';
	}
}

/* Template functions */
function get_setting($name) {
	return sql_action("SELECT * FROM settings WHERE name='".sql_escape($name)."';");
}

function get_setting_value($name) {
	$action= get_setting($name);
	return $action['value'];
}

function get_title() {
	return get_setting_value('title');
}

function get_description() {
	return get_setting_value('description');
}

function get_home_link() {
	return get_setting_value('base_link');
}

function link_to_feed($url, $title, $type) {
	return "<link href=\"$url\" rel=\"alternate\" type=\"application/$type+xml\" title=\"$title\" />";
}

function registrations_open() {
	if(get_setting_value('show_reg_btn') == 'on') {
		return true;
	} else {
		return false;
	}
}

function link_feeds() {
	$feeds= list_feeds();
	$html= '';

	for($n=0; $n < count($feeds); $n++) {
		$feed= $feeds[$n];
		$url= $feed['feedUrl'];
		$title= $feed['title'];
		$type= $feed['type'];
		$html.= "\t\t".link_to_feed($url, $title, $type)."\n";
	}

	echo $html;
}

function planetoid_feed() {
	echo "\t\t".link_to_feed(get_home_link().'/feed.php', get_title(), 'rss')."\n";
}

/* Plugins */
function get_plugin_setting($plugin_name, $setting) {
	return get_setting_value("plugin_{$plugin_name}:{$setting}");
}

function update_plugin_setting($plugin_name, $setting, $value) {
	if($setting != 'active') {
		return sql_query("UPDATE settings SET value='".sql_escape($value)."' WHERE name='plugin_{$plugin_name}:{$setting}';");
	} else {
		return false;
	}
}

function plugin_prepare_db($rows, $plugin_name) {
	while(list($name, $value) = each($rows)) {
		sql_query("INSERT INTO settings VALUES (".sql_autoid('settings').", 'plugin_{$plugin_name}:{$name}', '{$value}');");
	}
}

function is_plugin_active($name) {
	$q= sql_action("SELECT value FROM settings WHERE name='plugin_{$name}:active';");

	if($q['value'] == 'true') {
		return true;
	} else {
		return false;
	}
}

function list_active_plugins() {
	$q= sql_get_array("SELECT name FROM settings;");
	$plugins= array();

	for($n=0; $n < count($q); $n++) {
		if(preg_match('/plugin_([^.]+):active/i', $q[$n]['name'], $plugin_match)) {
			$plugins[]= $plugin_match[1];
		}
	}

	return $plugins;
}

function plugin_load_file($name) {
	$file_path= "inc/plugins/{$name}/plugin.info";
	if(file_exists($file_path)) {
		$file= fopen($file_path, "r");
		$plugin_info= fread($file, filesize($file_path));
		$plugin_info= parse_info_file($plugin_info, 'Plugin Info');
		fclose($file);

		if($plugin_info) {
			return "inc/plugins/{$name}/{$plugin_info['LoadFile']}";
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function checkpoint($location, $data=false) {
	global $_PLUGINS;

	if(isset($_PLUGINS[$location])) {
		for($n=0; $n < count($_PLUGINS[$location]); $n++) {
			$_PLUGINS[$location][$n]($data);
		}
	}
}

function plugin_attach($location, $fn_name) {
	global $_PLUGINS;

	$_PLUGINS[$location][]= $fn_name;
}


/* Misc functions */
function parse_info_file($file, $type) {
	$file= explode("\n", $file);
	$result= array();

	if(strtolower($file[0]) == '['.strtolower($type).']') {
		for($n=0; $n < count($file); $n++) {
			$line= $file[$n];
			if(strrpos($line, "#") === false) {
				$set= explode('=', $line);
				if(isset($set[0]) && isset($set[1])) {
					$result[$set[0]]= $set[1];
				}
			}
		}
	} else {
		$result= false;
	}

	return $result;
}

function list_info_dir($dir, $info_name) {
	$list= array();
	if($handle = opendir($dir)) {
		while(false != ($file= readdir($handle))) {
			$path= $dir.'/'.$file.'/'.$info_name.'.info';
			if(is_dir($dir.'/'.$file) && $file != '.' && $file != '..' && file_exists($path)) {
					$_doc= fopen($path, 'r');
					$doc= fread($_doc, filesize($path));
					$list[]= array(parse_info_file($doc, ucfirst($info_name).' Info'), $file);
					fclose($_doc);
			}
		}
		closedir($handle);
	} else {
		return false;
	}

	return $list;
}


function running_time() {
	return date('jS F Y.', get_setting_value('install_time'));
}

function simplepie_linkback() {
	$simplepie= new SimplePie();
	return $simplepie->linkback;
}

function simplepie_version() {
	$simplepie= new SimplePie();
	return $simplepie->version;
}

function curl_get($url) {
	if(function_exists('curl_init')) {
		$session= curl_init($url);

		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		$output= curl_exec($session);

		curl_close($session);
	} else {
		$output= false;
	}

	return $output;
}

function cache_average_time() {
	$log_f= fopen(CACHE_DIR.'/cron.log', 'r');
	$log= fread($log_f, filesize(CACHE_DIR.'/cron.log'));
	fclose($log_f);
	$lines= explode("\n", $log);
	$total= 0;

	for($n=0; $n < count($log); $n++) {
		$line= $lines[$n];
		$line= explode("|", $line);
		$line= $line[1];
		$total .= $line;
	}

	return round(($total/(count($lines) - 1)), 2);
}

function hAtom_date($timestamp) {
	return date('Ymd', $timestamp);
}

function eroo($no, $str, $file, $line) {
	switch($no) {
		case E_USER_NOTICE: case E_NOTICE:
			$halt = false;
			$type = "Notice";
			break;
		case E_USER_WARNING: case E_COMPILE_WARNING:
		case E_CORE_WARNING: case E_WARNING:
			$halt = false;
			$type = "Warning";
			break;
		case E_USER_ERROR: case E_COMPILE_ERROR:
		case E_CORE_ERROR: case E_ERROR:
			$halt = true;
			$type = "Fatal Error";
			break;
		case E_PARSE:
			$halt = true;
			$type = "Parse Error";
			break;
		case P_DB_ERROR:
			$halt = true;
			$type = "Database error";
			break;
		case P_DB_QUERY_ERROR:
			$halt = true;
			$type = "Database query error";
			break;
		default:
			$halt = false;
			$type = "Unknown Error";
	}

	if($halt) {
		$halted = "True";
	} else {
		$halted = "False";
	}

	if(DEBUG == true) {
		$f = fopen($file, 'r');
		$lines = fread($f, filesize($file));
		fclose($f);
		$lines = explode("\n", $lines);
		$lines= highlight_string($lines[$line-2]."\n".$lines[$line-1]."\n".$lines[$line], true);

		$debug = $lines
						."<ul>"
							."<li>File: <pre style=\"display:inline\">{$file}</pre></li>"
							."<li>Line: {$line}</li>"
							."<li>Type: {$type}</li>"
							."<li>On: ".date("r")."</li>"
							."<li>Script stopped: {$halted}</li>"
						."</ul>";
	} else {
		if(strstr($type, 'Database') !== false) {
			$debug = "Please notify administrator about this issue.<br/>Or if you are one check your configuration file.";
		} else {
			$debug = "";
		}
	}

	if($halt || DEBUG) {
		echo "<div style=\"margin:20px auto;display:block;padding:10px;border:1px solid #c0c0c0;width:400px;background:#fff;\">"
			."<h1 style=\"font-weight:normal;font-size:1.2em;color:#900;margin-top:0;\">{$str}</h1>"
			."<p style=\"font-size:0.9em;color:#000;margin-left:10px;\">{$debug}</p></div>";

		if($halt)
			die();
	}

	return true;
}

set_error_handler('eroo');
?>
