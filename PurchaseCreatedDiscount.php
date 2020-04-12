<?php

namespace SyntaxSociety\PurchaseCreated;

require_once('Model.php');
require_once('Price.php');

class Discount extends \Model {

	public function getFieldSpecifications(): array {
		return [
			'uuid' => 'string',
			'name' => 'string',
			'description' => 'string',
			'amount' => '\SyntaxSociety\Price',
			'percentage' => 'string',
			'imageLookupKeys' => 'string[]',
			'externalReference' => 'string',
			'etag' => 'string',
			'updated' => 'string',
			'updatedBy' => 'string',
			'created' => 'string',
		];
	}
}
