<?php declare(strict_types = 1);

namespace Tests\Mocks\Handler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Tests\Mocks\Message\FooMessage;
use Tests\Mocks\Message\BarMessage;

final class MultipleMethodsWithAttributesHandler
{
	#[AsMessageHandler]
	public function whenFooMessageReceived(FooMessage $message): void
	{
		// For tests
	}

	#[AsMessageHandler]
	public function whenBarMessageReceived(BarMessage $message): void
	{
		// For tests
	}
}
