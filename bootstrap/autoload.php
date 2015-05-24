<?php

function __autoload($class_name) {
	global $App;
	$dirs = array(
		'/bootstrap/',	
		'/Model/',
		'/controller/',
		'/controller/admin/',
		'/Libraries/',
		'/Provider/',
		'/Provider/Orm/'
	);
	foreach ($dirs as $dir) {
		if (file_exists($App->home  . $dir . $class_name . '.php')){
			$file = $App->home . $dir . $class_name . '.php';
			break;
		}
	}

	if (!empty($file)) {
		require_once $file;
	} else {
		return false;
	}
}