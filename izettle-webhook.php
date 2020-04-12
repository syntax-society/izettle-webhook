<?php

require_once('Event.php');

$headers = apache_request_headers();
$signature = $headers['X-iZettle-Signature'];

if ($signature === '') {
	http_response_code(200);
	die();
}

/*if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	$method = $_SERVER['REQUEST_METHOD'];
	echo <<<RESPONSE
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>405 Method Not Allowed</title>
</head><body>
<h1>Method Not Allowed</h1>
<p>The $method is not allowed for the requested URL.</p>
</body></html>
RESPONSE;
	die();
}*/

function saveMessage(string $filename, array $message) {
	$json = file_get_contents($filename);
	$messages = json_decode($json, true);

	if (array_key_exists('timestamp', $message) && $message['timestamp']) {
		$timestamp = $message['timestamp'];
	} else {
		$timestamp = (new DateTime())->format('Y-m-d\TH:i:s.v\Z');
	}

	$messages[$timestamp] = $message;

	$json = json_encode($messages, JSON_PRETTY_PRINT);
	file_put_contents($filename, $json, LOCK_EX);
}

// read config
$json = file_get_contents('config.json');
$config = json_decode($json, true);

// read request body
$body = file_get_contents('php://input');
$eventData = json_decode($body, true);

saveMessage($config['eventLogFilename'], $eventData);

$event = new Event($eventData);

if (!$event->isValid($signature, $signingKey)) {
	http_response_code(401);
	$url = $_SERVER['REQUEST_URI'];
	echo <<<RESPONSE
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>401 Method Not Allowed</title>
</head><body>
<h1>Method Not Allowed</h1>
<p>This server could not verify that you are authorized to access the URL "$url".</p>
<p>You either supplied the wrong credentials (e.g., bad password), or your browser doesn't understand how to supply the credentials required.</p>
<p>In case you are allowed to request the document, please check your user-id and password and try again.</p>
/body></html>
RESPONSE;
	die();
}

$event->handle();

$logger = new Logger(
	Logger::INFO,
	Logger::INFO,
	'log.txt'
);
$logger->debug('Webhook finished properly');
?>
