<?php

namespace SyntaxSociety\ProductUpdated;

use \znexx\Logger;

require_once(__DIR__ . '/vendor/autoload.php');
require_once('Model.php');
require_once('ProductUpdatedProduct.php');
require_once('WebhookEventInterface.php');

class ProductUpdated extends \Model implements \SyntaxSociety\WebhookEventInterface  {

	public function perform(Logger $logger): void {
		$productName = $this->newEntity->name;
		$logger->info("Produkt $productName uppdaterad");

		$arr1 = json_decode(json_encode($this->newEntity), true);
		$arr2 = json_decode(json_encode($this->oldEntity), true);
		$logger->info(arrDiff($arr1, $arr2));
	}

	public function getFieldSpecifications(): array {
		return [
			'organizationUuid' => 'string',
			'newEntity' => __NAMESPACE__ . '\Product',
			'oldEntity' => __NAMESPACE__ . '\Product',
		];
	}
}
