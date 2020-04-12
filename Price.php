<?php

namespace SyntaxSociety;

require_once('Model.php');

class VariantPrice extends \Model {
	public function getFieldSpecifications(): array {
		return [
			'amount' => 'int',
			'currencyId' => 'string',
		];
	}
}
