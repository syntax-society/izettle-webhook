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

	public function perform(Logger $logger, array $config): void {
		foreach ($this->products as $product) {
			$this->handleProduct($logger, $config, $product);
		}
	}

	protected function handleProduct(Logger $logger, array $config, Product $product) {
		switch ($product->name) {
		case 'Medlemsavgift':
			$nickname = $product->variantName;
			$dateTime = new \DateTime($this->created);
			$year = $dateTime->format('Y');
			$paidDate = $dateTime->format('Y-m-d');
			$amount = $product->unitPrice / 100; // because enhet är ören

			try {
				$this->performInsert(
					$config,
					'member_fees',
					[
						'nickname' => $nickname,
						'year' => $year,
						'paid_date' => $paidDate,
						'amount' => $amount,
					]
				);
				$logger->info("Medlemsavgift för $nickname betald");
			} catch (\Exception $e) {
				$logger->error($e->getMessage());
			}
			break;
		case 'Partyavgift':
			$nickname = $product->variantName;
			$partyTitle = 'herp derp party';
			$partyDate = '2001-01-01';
			$fee = 100;
			$paid = 100;

			try {
				$this->performInsert(
					$config,
					'member_fees',
					[
						'nickname' => $nickname,
						'party_title' => $partyTitle,
						'party_date' => $paidDate,
						'fee' => $fee,
						'paid' => $paid,
					]
				);
				$logger->info("Partyavgift för $nickname betald");
			} catch (\Exception $e) {
				$logger->error($e->getMessage());
			}
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
