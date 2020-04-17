# izettle-webhook

PHP application for receiving events from iZettles webhook interface documented at [https://github.com/iZettle/api-documentation/blob/master/pusher.adoc](https://github.com/iZettle/api-documentation/blob/master/pusher.adoc) and [https://pusher.izettle.com/swagger](https://pusher.izettle.com/swagger).

## Important files

- `.htaccess`: A simple htaccess file that restricts access to all files except `izettle-webhook.php`
- `config.json` - A JSON file that should at least contain the following:

		{
			"signing_key": "(your signing key returned when creating the subscription)",
			"eventLogFilename": "(the name of the JSON file where events will be stored)"
			"logFilename": "(the name of the file for the regular logging)"
		}

- `CustomEvent.php`: The PHP class describing the whole event. This is essentially a PHP representation of the JSON data sent from iZettle.
- `composer.json`: The Composer configuration which specifies which PHP libraries we're using
- `izettle-webhook.php`: The endpoint for the webhook. This is the file that receives the HTTP requests from iZettle
- `test.php`: A simple PHP script that parses the list of received events and can replay them at will
