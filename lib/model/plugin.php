<?php

function activatePlugin($name) {
	global $database, $owner, $activePlugins;
	if (in_array($name, $activePlugins))
		return true;
	if (!ereg("^[[:alnum:] _\-]+$", $name))
		return false;
	if (!is_dir(ROOT . "/plugins/$name"))
		return false;
	if (!file_exists(ROOT . "/plugins/$name/index.xml") || !file_exists(ROOT . "/plugins/$name/index.php"))
		return false;
	$name = mysql_escape_string($name);
	mysql_query("INSERT INTO {$database['prefix']}Plugins VALUES ($owner, '$name', null)");
	return (mysql_affected_rows() == 1);
}
 
function deactivatePlugin($name) {
	global $database, $owner, $activePlugins;
	if (!in_array($name, $activePlugins))
		return false;
	$name = mysql_escape_string($name);
	mysql_query("DELETE FROM {$database['prefix']}Plugins WHERE owner = $owner AND name = '$name'");
	return true;
}

function getCurrentSetting( $name){
	global $database , $owner, $activePlugins;
	if( !in_array( $name , $activePlugins))
		return false;
	$name = mysql_escape_string( $name ) ;
	$result = mysql_query("SELECT settings FROM {$database['prefix']}Plugins WHERE owner = $owner AND name = '$name'");
	if( false === $result ) 
		return false;
	$out = mysql_fetch_array($result); 
	return $out['settings'];
}
function updatePluginConfig( $name , $setVal){
	global $database, $owner, $activePlugins;
	if (!in_array($name, $activePlugins))
		return false;
	$name = mysql_escape_string( $name ) ;
	$setVal = mysql_escape_string( $setVal ) ;
	mysql_query(
	"UPDATE {$database['prefix']}Plugins 
	SET settings = '$setVal' 
	WHERE owner = $owner 
	AND name = '$name'"
	);
	if( mysql_affected_rows() == 1 )
		return '0';
	return (mysql_error() == '') ? '0' : '1';
}
?>
