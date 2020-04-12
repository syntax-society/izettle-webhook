<?php

namespace SyntaxSociety\PurchaseCreated;

class PaymentAttributes extends \Model {

	function getFieldSpecifications(): array {
		return [
			'transactionStatusInformation' => 'string',
			'maskedPan' =>                    'string',
			'cardPaymentEntryMode' =>         'string',
			'referenceNumber' =>              'string',
			'cardType' =>                     'string',
			'terminalVerificationResults' =>  'string',
			'applicationIdentifier' =>        'string',
			'applicationName' =>              'string',
		];
	}
}
