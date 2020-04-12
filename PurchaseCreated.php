<?php

namespace SyntaxSociety\PurchaseCreated;

require_once('Model.php');
require_once('PurchaseCreatedDiscount.php');
require_once('PurchaseCreatedPayment.php');
require_once('PurchaseCreatedProduct.php');
require_once('PurchaseCreatedReference.php');
require_once('WebhookEventInterface.php');

use znexx\Logger;

class PurchaseCreated extends \Model implements \SyntaxSociety\WebhookEventInterface {

	public function perform(Logger $logger): void {
		foreach ($this->products as $product) {
			$this->handleProduct($logger, $product);
		}
	}

	protected function handleProduct(Logger $logger, Product $product) {
		switch ($product->name) {
		case 'Medlemsavgift':
			$nickname = $product->variantName;
			$logger->info("Medlemsavgift för $nickname betald");
			break;
		}
	}

	function getFieldSpecifications(): array {
		return [
			'purchaseUuid' =>     'string',
			'source' =>           'string',
			'userUuid' =>         'string',
			'currency' =>         'string',
			'country' =>          'string',
			'amount' =>           'int',
			'vatAmount' =>        'int',
			'timestamp' =>        'int',
			'created' =>          'string',
			'purchaseNumber' =>   'int',
			'userDisplayName' =>  'string',
			'udid' =>             'string',
			'organizationUuid' => 'string',
			'products' =>         __NAMESPACE__ . '\Product[]',
			'discounts' =>        __NAMESPACE__ . '\Discount[]',
			'payments' =>         __NAMESPACE__ . '\Payment[]',
			'references' =>       __NAMESPACE__ . '\Reference',
		];
	}
}
