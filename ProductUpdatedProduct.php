<?php

namespace SyntaxSociety\ProductUpdated;

require_once('Model.php');
require_once('ProductUpdatedPresentation.php');
require_once('ProductUpdatedVariant.php');
require_once('ProductUpdatedVariantOptionDefinitions.php');

class Product extends \Model {
	public function getFieldSpecifications(): array {
		return [
			'uuid' => 'string',
			'organizationUuid' => 'string',
			'name' => 'string',
			'presentation' => __NAMESPACE__ . '\Presentation',
			'variants' => __NAMESPACE__ . '\Variant[]',
			'vatPercentage' => 'string',
			'variantOptionDefinitions' => __NAMESPACE__ . '\VariantOptionDefinitions',
			'etag' => 'string',
			'updated' => 'string',
			'updatedByUserUuid' => 'string',
			'created' => 'string',
		];
	}
}
