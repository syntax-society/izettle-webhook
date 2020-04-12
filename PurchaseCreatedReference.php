<?php

namespace SyntaxSociety\PurchaseCreated;

require_once('Model.php');

class Reference extends \Model {

	function __construct($data = []) {
		parent::__construct($data);
	}

	public function getFieldSpecifications(): array {
		return [
			'checkoutUUID' => 'string',
		];
	}
}
