<?php

require_once('lib/unrested/unrested.php');
require_once('db.php');

$unrested = new unrested();

$unrested->register('POST', '/file_search/{integer:publishedfileid}', function($input){
	$hash_list = array();
	foreach($input as $key => $data){
		if(!isset($data['sha1'])){
			continue;
		}
		$hash_list[] = $data['sha1'];
	}

	return array(200, db_file_search($input['publishedfileid'], $hash_list));
});

$unrested->run();
