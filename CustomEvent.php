<?php

require_once(__DIR__ . '/SyntaxSql.php');
require_once(__DIR__ . '/vendor/autoload.php');

use znexx\Logger;
use znexx\iZettle\webhook\PurchaseCreated;
use znexx\iZettle\webhook\PurchaseCreated\Product;
use znexx\iZettle\webhook\ProductUpdated;
use znexx\iZettle\webhook\Event;

class CustomEvent extends Event {

	protected $logger;
	protected $config;
	protected $sql;

	function __construct(Logger $logger, array $config, array $data = []) {
		$this->logger = $logger;
		$this->config = $config;

		$this->sql = new SyntaxSql($config);

		parent::__construct($data);
	}

	public function handle() {
		if (!$this->eventName) {
			throw new \Exception('Event name missing!');
		}
		$data = json_decode($this->payload, true);


		switch ($this->eventName) {
		case 'ProductUpdated':
			$e = new ProductUpdated($data);
			$this->handleProductUpdated($e);
			break;
		case 'PurchaseCreated':
			$e = new PurchaseCreated($data);
			$this->handlePurchaseCreated($e);
			break;
		}
	}

	protected function handleProductUpdated(ProductUpdated $productUpdated) {
		$productName = $productUpdated->newEntity->name;
		$this->logger->info("Produkt $productName uppdaterad");

		$arr1 = json_decode(json_encode($productUpdated->newEntity), true);
		$arr2 = json_decode(json_encode($productUpdated->oldEntity), true);
	}

	protected function handlePurchaseCreated(PurchaseCreated $purchaseCreated) {
		foreach ($purchaseCreated->products as $product) {
			$this->handleProductPurchased($product, $purchaseCreated->created);
		}
	}

	protected function handleProductPurchased(Product $product, string $createdTimestamp) {
		switch ($product->name) {
		case 'Medlemsavgift':
			$nickname = $product->variantName;
			$dateTime = new \DateTime($createdTimestamp);

			try {
				if ($product->quantity > 0) {
					$inserted = $this->sql->performInsert(
						'member_fees',
						[
							'nickname' => $nickname,
							'year' => $dateTime->format('Y'),
							'paid_date' => $dateTime->format('Y-m-d'),
							'amount' => $product->unitPrice / 100,
						]
					);
					if ($inserted) {
						$this->logger->info("Medlemsavgift för $nickname betald");
					} else {
						$this->logger->error("Kunde ej lägga in medlemsavgift för $nickname");
					}
				} elseif ($product->quantity < 0) {
					$deleted = $this->sql->performDelete(
						'member_fees',
						[
							'nickname' => $nickname,
							'year' => $dateTime->format('Y'),
							'paid_date' => $dateTime->format('Y-m-d'),
							'amount' => $product->unitPrice / 100,
						]
					);
					if ($deleted) {
						$this->logger->info("Medlemsavgift för $nickname returnerad!");
					} else {
						$this->logger->error("Kunde ej ta bort medlemsavgift för $nickname");
					}
				}
			} catch (\Exception $e) {
				$this->logger->error($e->getMessage());
			}
			break;
		case 'Partyavgift':
			$nickname = $product->variantName;
			$partyTitle = 'herp derp party';
			$partyDate = '2001-01-01';
			$fee = 100;
			$paid = 100;

			try {
				$this->sql->performInsert(
					'member_fees',
					[
						'nickname' => $nickname,
						'party_title' => $partyTitle,
						'party_date' => $paidDate,
						'fee' => $fee,
						'paid' => $paid,
					]
				);
				$this->logger->info("Partyavgift för $nickname betald");
			} catch (\Exception $e) {
				$this->logger->error($e->getMessage());
			}
			break;
		}
	}
}
