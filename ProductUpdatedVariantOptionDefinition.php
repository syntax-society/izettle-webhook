<?php

namespace SyntaxSociety\ProductUpdated;

require_once('Model.php');
require_once('ProductUpdatedVariantOptionProperties.php');

class VariantOptionDefinition extends \Model {
	public function getFieldSpecifications(): array {
		return [
			'name' => 'string',
			'properties' => __NAMESPACE__ . '\VariantOptionProperties[]',
		];
	}
}
