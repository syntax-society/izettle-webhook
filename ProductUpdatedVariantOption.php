<?php

namespace SyntaxSociety\ProductUpdated;

require_once('Model.php');

class VariantOption extends \Model {
	public function getFieldSpecifications(): array {
		return [
			'name' => 'string',
			'value' => 'string',
		];
	}
}
