<?php
require('../config.php');
include('../planetoid.php');

$file= curl_get('http://planetoid-project.org/versions.info');
if($file) {
	$l_version= parse_info_file($file, 'Planetoid Versions');
	$V= $l_version['LatestVersion'];
	$SV= $l_version['LatestVersionRevision'];
	$download= $l_version['LatestVersionGetURL'];
	
	if(PLANETOID_VERSION.PLANETOID_REVISION < $V.$SV) {
		$output= "There is newer version of Planetoid ($V.$SV), you can get it at <a href=\"$download\">$download</a>";
	} else {
		$output= "You have the latest version";
	}
} else {
	$output= "Unable to check for updates";
}
if($_POST['ajax'] == 'true') {
	echo $output;
} else {
	if($file) {
		header("Location: {$_GET['r_to']}?v=$V&sv=$SV&link=$download");
	} else {
		header("Location: {$_GET['r_to']}?e=no-curl");
	}
}

?>