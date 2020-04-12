#!/usr/bin/php -q
<?php

$json = file_get_contents('config.json');
$config = json_decode($json, true);

function clearLog($filename) {
	$json = json_encode([], JSON_PRETTY_PRINT);
	file_put_contents($filename, $json, LOCK_EX);
}

clearLog($config['eventLogFilename']);
