<?php declare(strict_types = 1);

namespace Contributte\Messenger\DI\Utils;

use Contributte\Messenger\Exception\LogicalException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionUnionType;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

final class Reflector
{

	/**
	 * @param class-string $class
	 * @return array<AsMessageHandler>
	 */
	public static function getMessageHandlers(string $class): array
	{
		$rc = new ReflectionClass($class);

		$classAttributes = \array_map(
			static fn (ReflectionAttribute $attribute): AsMessageHandler => $attribute->newInstance(),
			$rc->getAttributes(AsMessageHandler::class),
		);

		$methodAttributes = [];

		foreach ($rc->getMethods() as $method) {
			$methodAttributes[] = array_map(
				static function (ReflectionAttribute $reflectionAttribute) use($method): AsMessageHandler {
					$attribute = $reflectionAttribute->newInstance();
					$attribute->method = $method->getName();

					return $attribute;
				},
				$method->getAttributes(AsMessageHandler::class),
			);
		}

		return \array_merge($classAttributes, ...$methodAttributes);
	}

	/**
	 * @param class-string $class
	 * @param array{method: string} $options
	 */
	public static function getMessageHandlerMessage(string $class, array $options): string
	{
		try {
			$rc = new ReflectionClass($class);
		} catch (ReflectionException $e) {
			throw new LogicalException(sprintf('Handler "%s" class not found', $class), 0, $e);
		}

		try {
			$rcMethod = $rc->getMethod($options['method']);
		} catch (ReflectionException) {
			throw new LogicalException(sprintf('Handler must have "%s::%s()" method.', $class, $options['method']));
		}

		if ($rcMethod->getNumberOfParameters() !== 1) {
			throw new LogicalException(sprintf('Only one parameter is allowed in "%s::%s()."', $class, $options['method']));
		}

		/** @var ReflectionNamedType|ReflectionUnionType|ReflectionIntersectionType|null $type */
		$type = $rcMethod->getParameters()[0]->getType();

		if ($type === null) {
			throw new LogicalException(sprintf('Cannot detect parameter type for "%s::%s()."', $class, $options['method']));
		}

		if ($type instanceof ReflectionUnionType) {
			throw new LogicalException(sprintf('Union parameter type for "%s::%s() is not supported."', $class, $options['method']));
		}

		if ($type instanceof ReflectionIntersectionType) {
			throw new LogicalException(sprintf('Intersection parameter type for "%s::%s() is not supported."', $class, $options['method']));
		}

		return $type->getName();
	}

}
