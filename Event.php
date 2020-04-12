<?php

require_once('Model.php');
require_once('ProductUpdated.php');
require_once('PurchaseCreated.php');

use znexx\Logger;

class Event extends \Model {

	public function getFieldSpecifications(): array {
		return [
			'organizationUuid' => 'string',
			'messageUuid' => 'string',
			'eventName' => 'string',
			'messageId' => 'string',
			'payload' => 'string',
			'timestamp' => 'string',
		];
	}

	protected function isValid(string $signature, string $signingKey): bool {
		$timestampPayload = stripslashes($this->timestamp . '.' . $this->payload);
		$calculatedSignature = hash_hmac('sha256', $timestampPayload, $signingKey);

		return $signature === $calculatedSignature;
	}

	public function handle(Logger $logger, array $config) {
		if (!$this->eventName) {
			throw new \Exception('Event name missing!');
		}
		$data = json_decode($this->payload, true);

		$className = 'SyntaxSociety\\' . $this->eventName . '\\' . $this->eventName;
		$e = new $className($data);
		$e->perform($logger, $config);
	}
}
