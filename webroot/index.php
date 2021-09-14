<?php

function is_mike2(): bool{
	if($_SERVER["REMOTE_ADDR"] === "24.178.226.180"){return true;}
	$actualSubdomain = explode('.', $_SERVER['HTTP_HOST'] ?? "")[0] ?? "";
	if($actualSubdomain === "local"){return true;}
	if($_SERVER["SERVER_ADDR"] === "127.0.0.1" && $_SERVER["REMOTE_ADDR"] === "127.0.0.1"){return true;}
	return false;
}
if(!is_mike2()){
	echo "You're not mike!";
	return;
}

use XHGui\Application;

require dirname(__DIR__) . '/vendor/autoload.php';

$app = new Application();
$app->run();
