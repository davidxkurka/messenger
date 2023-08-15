<?php declare(strict_types = 1);

namespace Tests\Mocks\Handler;

use Tests\Mocks\Message\FooMessage;
use Tests\Mocks\Message\BarMessage;

final class MultipleMethodsWithoutAttributesHandler
{
	public function whenFooMessageReceived(FooMessage $message): void
	{
		// For tests
	}

	public function whenBarMessageReceived(BarMessage $message): void
	{
		// For tests
	}
}
