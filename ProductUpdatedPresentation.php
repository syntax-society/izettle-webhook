<?php

namespace SyntaxSociety\ProductUpdated;

require_once('Model.php');

class Presentation extends \Model {
	public function getFieldSpecifications(): array {
		return [
			'imageUrl' => 'string',
			'backgroundColor' => 'string',
			'textColor' => 'string',
		];
	}
}
