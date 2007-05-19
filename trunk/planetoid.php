<?php
define('PLANETOID_VERSION', '0.0');
define('PLANETOID_REVISION', '7');
	
if(SQL_TYPE == 'pgsql') {
	$db_link= pg_connect('host='.SQL_HOST.' port='.SQL_PORT.' dbname='.SQL_DB_NAME.' user='.SQL_USER.' password='.SQL_PASS)
		or die('Could not connect: ' . pg_last_error());
} else if(SQL_TYPE == 'mysql') {
	$db_link= mysql_connect(SQL_HOST, SQL_USER, SQL_PASS)
		or die('Could not connect: ' . mysql_error());
	mysql_select_db(SQL_DB_NAME) or die('Fatal error: Failed to open connection to MySQL!<br/>Check your configuration');
}

define('BASE_DIR', get_setting_value('base_url'));
define('CACHE_DIR', dirname(__FILE__).'/cache');

$_PLUGINS= array();

$feeds= array();
$feeds_d= array();
$feeds_ch= CACHE_DIR.'/feeds_base.spc';
$feeds_d_ch= CACHE_DIR.'/feeds_details.spc';


if(!are_feeds_cached()) {
	if(SQL_TYPE == 'pgsql') {
		$feeds_q= sql_get_array("SELECT * FROM feeds;");
		for($n=0; $n < count($feeds_q); $n++) {
			$feed= $feeds_q[$n];
			
			if($feed['approved'] == 1) {
				$feeds[]= $feed['url'];
			}
			
			$feeds_d[]= $feed;
		};
	};
	
	cache(serialize($feeds), $feeds_ch);
	cache(serialize($feeds_d), $feeds_d_ch);
} else {
	$feeds= get_cache($feeds_ch);
	$feeds_d= get_cache($feeds_d_ch);
};

/* Feed functions */
function list_articles() {
	$list_articles_ch= CACHE_DIR.'/list_articles.spc';
	
	if(is_cached($list_articles)) {
		return get_cache($list_articles_ch);
	} else {
		global $feeds;
		$feed= array(count($feeds));
		
		$articles= array();
		$date_format= get_setting_value("date_format");
		for($n=0; $n < count($feeds); $n++) {
			$feed[$n]= new SimplePie();
			$feed[$n]->strip_ads(true);
			$feed[$n]->remove_div();
			
			if($MOBILE) {
				$feed[$n]->strip_attributes(array('align'));
				$feed[$n]->strip_htmltags(array('p', 'div', 'blockquote', 'pre', 'code', 'img'));
			}
			
			$feed[$n]->feed_url($feeds[$n]);
			$feed[$n]->init();
			$link= $feed[$n]->get_feed_link();
			
	// 		if(strlen($link) < 5) {
	// 			$link= explode('http://', $feed[$n]);
	// 			$link= explode('/', $link[1]);
	// 			$link= 'http://' . $link[0] . '/';
	// 		}
			
			$avatar= sql_action("SELECT avatar FROM feeds WHERE url='$feeds[$n]';");
			$avatar= $avatar['avatar'];
			
// 			if($avatar != 'inc/images/no-avatar.png') {
// 				$avatar= urlencode($avatar['avatar']);
// 			}
			
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
							'permalink' => $item->get_permalink(),
							'description' => $content,
							'post_time' => $item->get_date($date_format),
							'avatar_url' => $avatar
						);
						
						$articles[$key]['plugin_data']= checkpoint("article", $articles[$key]);
					}
				}
			}
		}
		
		ksort($articles);
		$articles= array_reverse($articles, false);
		
		$article_limit= get_setting_value('posts_num');
		if($article_limit != 0) {
			array_splice($articles, $article_limit);
		}
		
		cache(serialize($articles), $list_articles_ch);
		return $articles;
	}
};

function list_feeds() {
	$list_feeds_ch= CACHE_DIR.'/list_feeds.spc';
	if(is_cached($list_feeds_ch)) {
		return get_cache($list_feeds_ch);
	} else {
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
		
		cache(serialize($list), $list_feeds_ch);
		return $list;
	}
};

function remove_feed($id) {
	global $feeds, $feeds_d;
	
	for($n=0; $n < count($feeds_d); $n++) {
		if($feeds_d[$n]['id'] == $id) {
			unset($feeds_d[$n]);
			unset($feeds[$n]);
			break;
		}
	}
};

function add_feed($feed_d) {
	global $feeds, $feeds_d;
	
	$feeds_d[]= $curr_feed_d;
	$feeds[]= $curr_feed_d['url'];
}

/* Caching functions */
function is_cached($path) {
	if(file_exists($path)) {
		$file= fopen($path, 'rw');
		$maketime= filemtime($path);
		$fileage= time() - $maketime;
		
		if(3600 > $fileage) {
			return true;
		} else {
			return false;
		}
		
		fclose($file);
	} else {
		return false;
	}
};

function are_feeds_cached() {
	global $feeds_ch, $feeds_d_ch;
	
	if(is_cached($feeds_ch) == true || is_cached($feeds_d_ch) == true) {
		return true;
	} else {
		return false;
	}
};

function cache($content, $path) {
	if(file_exists($path)) {
		$file= fopen($path, 'a+');
		$maketime= filemtime($path);
		$fileage= time() - $maketime;
		fwrite($file, $content);
		fclose($file);
	} else {
		$file= fopen($path, 'a+');
		fwrite($file, $content);
		fclose($file);
	}
};

function get_cache($path) {
	if(file_exists($path)) {
		$file= fopen($path, 'r');
		$cache= fread($file, filesize($path));
		return unserialize($cache);
		fclose($file);
	} else {
		return false;
	}
};

function refresh_cache() {
	$cache_files= array('list_feeds', 'list_articles', 'feeds_base', 'feeds_details');
	
	for($n=0; $n < count($cache_files); $n++) {
		$file= CACHE_DIR.'/'.$cache_files[$n].'.spc';
		if(file_exists($file)) {
			unlink($file);
		}
	}
	
	sleep(1);
// 	list_feeds();
// 	list_articles();
	
	return true;
}

/* SQL functions */

function sql_close() {
	global $db_link;
	if(SQL_TYPE == 'pgsql') {
		pg_close($db_link);
	} else if(SQL_TYPE == 'mysql') {
		mysql_close($db_link);
	}
};

function sql_action($action) {
	if(SQL_TYPE == 'pgsql') {
		$db_q= pg_query($action);
		$db_r= pg_fetch_array($db_q, NULL, PGSQL_ASSOC);
	} else if(SQL_TYPE == 'mysql') {
		$db_q= mysql_query($action) or die('Error:'.mysql_error());
		$db_r= mysql_fetch_array($db_q, MYSQL_ASSOC);
	};
	
	return $db_r;
};

function sql_query($action) {
	if(SQL_TYPE == 'pgsql') {
		return pg_query($action);
	} else if(SQL_TYPE == 'mysql') {
		return mysql_query($action) or die('Error:'.mysql_error());
	};
};

function sql_fetch_array($qo) {
	if(SQL_TYPE == 'pgsql') {
		$db_r= "pg_fetch_array($qo, NULL, PGSQL_ASSOC);";
	} else if(SQL_TYPE == 'mysql') {
		$db_r= "mysql_fetch_array($qo, MYSQL_ASSOC);";
	};
	
	return $db_r;
};

function sql_escape($string) {
	if(SQL_TYPE == 'pgsql') {
		return pg_escape_string($string);
	} else if(SQL_TYPE == 'mysql') {
		return mysql_escape_string($string);
	}
};

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
};

function sql_autoid($table) {
	if(SQL_TYPE == 'pgsql') {
		return "nextval('{$table}_id_seq')";
	} else if(SQL_TYPE == 'mysql') {
		return 'NULL';
	}
};

/* Template functions */
function get_setting($name) {
	return sql_action("SELECT * FROM settings WHERE name='".sql_escape($name)."';");
};

function get_setting_value($name) {
	$action= get_setting($name);
	return $action['value'];
};

function get_title() {
	return get_setting_value('title');
};

function get_description() {
	return get_setting_value('description');
};

function get_home_link() {
	return get_setting_value('base_link');
};

function link_to_feed($url, $title, $type) {
	return "<link href=\"$url\" rel=\"alternate\" type=\"application/$type+xml\" title=\"$title\" />";
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
};

function planetoid_feed() {
	echo "\t\t".link_to_feed(get_home_link().'/feed.php', get_title(), 'rss')."\n";
};

/* Plugins */
function get_plugin_setting($plugin_name, $setting) {
	return get_setting_value("plugin_{$plugin_name}:{$setting}");
};

function update_plugin_setting($plugin_name, $setting, $value) {
	return sql_query("UPDATE settings SET value='".sql_escape($value)."' WHERE name='plugin_{$plugin_name}:{$setting}';");
};

function plugin_prepare_db($rows, $plugin_name) {
	if($rows) {
		while(list($name, $value) = each($rows)) {
			sql_query("INSERT INTO settings VALUES (".sql_autoid('settings').", 'plugin_{$plugin_name}:$name', '$value');");
		}
	} else {
		return false;
	}
};

function is_plugin_active($name) {
	$q= sql_action("SELECT value FROM settings WHERE name='plugin_{$name}:active';");
	if($q['value'] == 'true') {
		return true;
	} else {
		return false;
	}
};

function list_active_plugins() {
	$q= sql_get_array("SELECT * FROM settings;");
	$plugins= array();
	
	for($n=0; $n < count($q); $n++) {
		if(preg_match('/plugin_([^.]+):active/i', $q[$n]['name'], $plugin_match)) {
			$plugins[]= $plugin_match[1];
		}
	}
	
	return $plugins;
};

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
};

function checkpoint($location, $data=false) {
	global $_PLUGINS;
	
	for($n=0; $n < count($_PLUGINS[$location]); $n++) {
		$_PLUGINS[$location][$n]($data);
	}
};

function plugin_attach($location, $fn_name) {
	global $_PLUGINS;
	
	$_PLUGINS[$location][]= $fn_name;
};


/* Misc functions */
function parse_info_file($file, $type) {
	$file= explode("\n", $file);
	$result= array();
	
	if(strtolower($file[0]) == '['.strtolower($type).']') {
		for($n=0; $n < count($file); $n++) {
			$line= $file[$n];
			if($line{0} != '#') {
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
};

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
};


function running_time() {
	return date('jS F Y.', get_setting_value('install_time'));
};

function simplepie_linkback() {
	$simplepie= new SimplePie();
	return $simplepie->linkback;
};

function simplepie_version() {
	$simplepie= new SimplePie();
	return $simplepie->version;
};

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
};
?>
