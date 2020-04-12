<?php

namespace SyntaxSociety;

interface WebhookEventInterface {
	public function perform(): void;
}
