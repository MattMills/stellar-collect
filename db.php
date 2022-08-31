<?php

date_default_timezone_set('UTC');

$dbh = pg_pconnect('user=postgres dbname=stellar-mods ');
assert_options(ASSERT_CALLBACK, function($parameter){
        error_log('Assert Failed: ' . print_r($parameter, True));
        throw new Exception('Assert failed');
});


function db_file_search($publishedfileid, $hash_list){
	global $dbh;

	$hash_string = '';

	foreach($hash_list as $key => $hash){
		$hash_string .= pg_escape_literal($hash) . ',';
	}

	$hash_string = substr($hash_string, 0, -1);

	$result = pg_query_params($dbh, 
		'WITH sha1_file_count as ('.
		'	    select mod_uuid, count(mod_uuid) as sha1_count from mods_filelist  where sha1 in ('. $hash_string. ')'.
		'		group by mod_uuid order by count(mod_uuid) desc'.
		'), total_file_count as ('.
		'	    select mod_uuid, count(mod_uuid) as total_count from mods_filelist where mod_uuid in (select mod_uuid from sha1_file_count) group by mod_uuid order by count(mod_uuid) asc'.
		'), max_revision as ('.
		'	select publishedfileid, max(revision_change_number) as max_rev from mods where publishedfileid = $1 group by publishedfileid'.
		') '.
		'SELECT mods.title, mods.revision_change_number, max_revision.max_rev, sfc.mod_uuid, sha1_count, total_count, total_count-sha1_count as difference '.
		' from mods '.
		' join sha1_file_count sfc on (sfc.mod_uuid = mods.uuid)'.
		' join total_file_count tfc on (sfc.mod_uuid = tfc.mod_uuid)'.
		' left join max_revision on (mods.publishedfileid = max_revision.publishedfileid)'.
		' where mods.publishedfileid = $1' .
		' order by  sha1_count desc, abs(total_count-sha1_count) asc, total_count desc',


/*
		'select mods.uuid, mods.title, mods.revision_change_number'.
		' from (mods_filelist as fl '.
		'  left join mods on fl.mod_uuid = mods.uuid '.
		' where '.
		'  fl.sha1 in ('.$hash_string. ') ',
 */
	
		array($publishedfileid)
	);
	
	return array('publishedfileid'=>$publishedfileid, 'matches' => pg_fetch_all($result));
}
