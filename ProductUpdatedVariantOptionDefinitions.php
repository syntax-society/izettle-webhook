<?php

namespace SyntaxSociety\ProductUpdated;

require_once('Model.php');
require_once('ProductUpdatedVariantOptionDefinition.php');

class VariantOptionDefinitions extends \Model {
	public function getFieldSpecifications(): array {
		return [
			'definitions' => __NAMESPACE__ . '\VariantOptionDefinition[]',
		];
	}
}
