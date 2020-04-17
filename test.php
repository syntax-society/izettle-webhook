#!/usr/bin/php -q
<?php

require_once __DIR__ . '/CustomEvent.php';
require_once __DIR__ . '/vendor/autoload.php';

use znexx\Logger;

$json = file_get_contents(__DIR__ . '/config.json');
$config = json_decode($json, true);

$body = file_get_contents(__DIR__ . '/' . $config['eventLogFilename']);
$messages = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

if ($argc !== 3) {
	echo 'Please provide a timestamp of the following:' . PHP_EOL;
	foreach (array_keys($messages) as $timestamp) {
		echo "$timestamp" . PHP_EOL;
	}
	echo 'And a signature (provide an invalid to get the calculated answer' . PHP_EOL;
	die();
}

$data = $messages[$argv[1]];

$logger = new Logger(
	Logger::DEEP_DEBUG,
);

$event = new CustomEvent($logger, $config, $data);

var_dump($event);

if (!$event->isValid($argv[2], $config['signing_key'])) {
	$logger->warning('Invalid signature provided');
	$logger->debug('Correct signature: ' . $event->calculateSignature($config['signing_key']));
} else {
	$event->handle();
}

$logger->info("testing done!");
