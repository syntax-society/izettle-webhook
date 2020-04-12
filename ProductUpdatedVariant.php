<?php

namespace SyntaxSociety\ProductUpdated;

require_once('Model.php');
require_once('Price.php');
require_once('ProductUpdatedVariantOption.php');

class Variant extends \Model {
	public function getFieldSpecifications(): array {
		return [
			'uuid' => 'string',
			'name' => 'string',
			'price' => __NAMESPACE__ . '\VariantPrice',
			'costPrice' => __NAMESPACE__ . '\VariantPrice',
			'options' => __NAMESPACE__ . '\VariantOption[]',
		];
	}
}
