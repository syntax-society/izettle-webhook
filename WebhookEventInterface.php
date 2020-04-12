<?php

namespace SyntaxSociety;

use znexx\Logger;

interface WebhookEventInterface {
	public function perform(Logger $logger, array $config): void;
}
