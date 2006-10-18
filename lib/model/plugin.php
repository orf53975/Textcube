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
	$name = mysql_real_escape_string($name);
	mysql_query("INSERT INTO {$database['prefix']}Plugins VALUES ($owner, '$name', null)");
	return (mysql_affected_rows() == 1);
}

function deactivatePlugin($name) {
	global $database, $owner, $activePlugins;
	if (!in_array($name, $activePlugins))
		return false;
	$name = mysql_real_escape_string($name);
	mysql_query("DELETE FROM {$database['prefix']}Plugins WHERE owner = $owner AND name = '$name'");
	return true;
}

function getCurrentSetting( $name){
	global $database , $owner, $activePlugins;
	if( !in_array( $name , $activePlugins))
		return false;
	$name = mysql_real_escape_string( $name ) ;
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
	$name = mysql_real_escape_string( $name ) ;
	$setVal = mysql_real_escape_string( $setVal ) ;
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
function treatPluginTable($plugin, $name, $fields, $keys, $version){
	global $database;
	if(doesExistTable($database['prefix'] . $name)){
		$keyname = 'Database_' . $name;
		$value = $plugin;		
		$query = "SELECT value FROM {$database['prefix']}ServiceSettings WHERE name='{$keyname}'";
		$result = DBQuery::queryCell($query);
		if (is_null($result)) {
			DBQuery::execute("INSERT INTO {$database['prefix']}ServiceSettings SET name='$keyname', value ='$value/$version'");
		} else {
			$values = explode('/', $result, 2);
			if (strcmp($plugin, $values[0]) != 0) { // diff plugin
				return false; // nothing can be done
			} else if (strcmp($version, $values[1]) != 0) {
				DBQuery::execute("UPDATE {$database['prefix']}ServiceSettings SET value ='$value/$version' WHERE name='$keyname'");
				$eventName = 'UpdateDB_' . $name;
				fireEvent($eventName, $values[1]);
			}
		}
		return true;
	} else {
		$query = "CREATE TABLE {$database['prefix']}{$name} (owner int(11) NOT NULL default '0',";

		foreach($fields as $field) {
			$isNull = ($field['isnull'] == 0) ? ' NOT NULL ' : ' NULL ';
			$defaultValue = is_null($field['default']) ? '' : " DEFAULT '" . mysql_real_escape_string($field['default']) . "' ";
			$fieldLength = ($field['length'] >= 0) ? "(".$field['length'].")" : '';
			$sentence = $field['name'] . " " . $field['attribute'] . $fieldLength . $isNull . $defaultValue . ",";
			$query .= $sentence;
		}
		
		array_unshift($keys, 'owner');
		$query .= " PRIMARY KEY (" . implode(',',$keys) . ")";
		$query .= ") TYPE=MyISAM DEFAULT CHARSET=utf8";
		
		if (DBQuery::execute($query)) {
				$keyname = 'Database_' . $name;
				$value = $plugin;
				DBQuery::execute("INSERT INTO {$database['prefix']}ServiceSettings SET name='$keyname', value ='$value/$version'");
			return true;
		}
		else return false;
		
	}
	return true;
}

function clearPluginTable($name) {
	global $database, $owner;
	$name = mysql_real_escape_string($name);
	mysql_query("DELETE FROM {$database['prefix']}{$name} WHERE owner = $owner");
	return (mysql_affected_rows() == 1);
}

function deletePluginTable($name) {
	global $database, $owner;
	if($owner !== 0) return false;
	$name = mysql_real_escape_string($name);
	mysql_query("DROP {$database['prefix']}{$name}");
	return true;
} 
?>