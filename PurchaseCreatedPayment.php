<?php

namespace SyntaxSociety\PurchaseCreated;

require_once('Model.php');
require_once('PurchaseCreatedPaymentAttributes.php');

class Payment extends \Model {
	private static $fields = [
		'uuid' =>       'string',
		'amount' =>     'string',
		'type' =>       'string',
		'createdAt' =>  'string',
		'attributes' => __NAMESPACE__ . '\PaymentAttributes',
	];

	function getFieldSpecifications(): array {
		return self::$fields;
	}
}
