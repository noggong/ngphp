<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");
require_once '../bootstrap/GlobalFunc.php';
require_once '../bootstrap/App.php';
require_once '../bootstrap/autoload.php';
require_once '../bootstrap/config.php';

require_once '../route.php';

$App->run();

