#!/usr/bin/php -q
<?php

require_once('Event.php');

$json = file_get_contents('config.json');
$config = json_decode($json, true);

$body = file_get_contents($config['eventLogFilename']);
$messages = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

if ($argc !== 3) {
	echo "Please provide a timestamp of the following:" . PHP_EOL;
	foreach (array_keys($messages) as $timestamp) {
		echo "$timestamp" . PHP_EOL;
	}
	print_r($argv);
	die();
}

$data = $messages[$argv[1] . ' ' . $argv[2]];

var_dump($data);

$event = new Event($data);
$event->handle($config);

function arrDiff(array $a1, array $a2):array {
    $r = array();
    foreach ($a1 as $k => $v) {
        if (array_key_exists($k, $a2)) {
            if ($v instanceof stdClass) {
                $rad = objDiff($v, $a2[$k]);
                if (count($rad)) { $r[$k] = $rad; }
            }else if (is_array($v)){
                $rad = arrDiff($v, $a2[$k]);
                if (count($rad)) { $r[$k] = $rad; }
            // required to avoid rounding errors due to the
            // conversion from string representation to double
            } else if (is_double($v)){
                if (abs($v - $a2[$k]) > 0.000000000001) {
                    $r[$k] = array($v, $a2[$k]);
                }
            } else {
                if ($v != $a2[$k]) {
                    $r[$k] = array($v, $a2[$k]);
                }
            }
        } else {
            $r[$k] = array($v, null);
        }
    }
    return $r;
}
