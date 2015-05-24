<?php
$path = '../config/';
$config_dir = scandir($path);

foreach ($config_dir as $dir) {
	
	$file_name = explode('.', $dir);

	$ext = array_pop($file_name);
	if ($ext == 'php') {

		$prop_name = implode('_', $file_name);
		$App->setConfig($prop_name, require $path . $dir);
	}
}
