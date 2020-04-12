# izettle-webhook

PHP application for receiving events from iZettles webhook interface documented at [https://github.com/iZettle/api-documentation/blob/master/pusher.adoc](https://github.com/iZettle/api-documentation/blob/master/pusher.adoc) and [https://pusher.izettle.com/swagger](https://pusher.izettle.com/swagger).

## Important files

- config.json - A JSON file that should at least contain the following:

		{
			"signing_key": "(your signing key returned when creating the subscription)",
			"eventLogFilename": "(the name of the JSON file where events will be stored)"
			"logFilename": "(the name of the file for the regular logging)"
		}

- izettle-webhook.php - The endpoint for the webhook. This is the file that receives the HTTP requests from iZettle
- .htaccess - A simple htaccess file that restricts access to all files except `izettle-webhook.php`
- composer.json - The Composer configuration which specifies which PHP libraries we're using
- Event.php - The PHP class describing the whole event. This is essentially a PHP representation of the JSON data sent from iZettle.
- Model.php - The superclass of a lot of these PHP classes. Serializes as JSON and sets properties according to an array received in its constructor. Also deserializes these child properties into corresponding objects.
