<?php

namespace SyntaxSociety\ProductUpdated;

require_once('Model.php');

class VariantOptionProperties extends \Model {
	public function getFieldSpecifications(): array {
		return [
			'value' => 'string',
			'imageUrl' => 'string',
		];
	}
}
