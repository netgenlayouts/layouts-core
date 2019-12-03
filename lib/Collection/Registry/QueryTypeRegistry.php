<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Registry;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\Collection\QueryType\QueryTypeInterface;
use Netgen\Layouts\Exception\Collection\QueryTypeException;
use Netgen\Layouts\Exception\RuntimeException;
use Traversable;

/**
 * @implements \IteratorAggregate<string, \Netgen\Layouts\Collection\QueryType\QueryTypeInterface>
 * @implements \ArrayAccess<string, \Netgen\Layouts\Collection\QueryType\QueryTypeInterface>
 */
final class QueryTypeRegistry implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * @var array<string, \Netgen\Layouts\Collection\QueryType\QueryTypeInterface>
     */
    private $queryTypes;

    /**
     * @param array<string, \Netgen\Layouts\Collection\QueryType\QueryTypeInterface> $queryTypes
     */
    public function __construct(array $queryTypes)
    {
        $this->queryTypes = array_filter(
            $queryTypes,
            static function (QueryTypeInterface $queryType): bool {
                return true;
            }
        );
    }

    /**
     * Returns if registry has a query type.
     */
    public function hasQueryType(string $type): bool
    {
        return isset($this->queryTypes[$type]);
    }

    /**
     * Returns a query type with provided identifier.
     *
     * @throws \Netgen\Layouts\Exception\Collection\QueryTypeException If query type does not exist
     */
    public function getQueryType(string $type): QueryTypeInterface
    {
        if (!$this->hasQueryType($type)) {
            throw QueryTypeException::noQueryType($type);
        }

        return $this->queryTypes[$type];
    }

    /**
     * Returns all query types.
     *
     * @param bool $onlyEnabled
     *
     * @return array<string, \Netgen\Layouts\Collection\QueryType\QueryTypeInterface>
     */
    public function getQueryTypes(bool $onlyEnabled = false): array
    {
        if (!$onlyEnabled) {
            return $this->queryTypes;
        }

        return array_filter(
            $this->queryTypes,
            static function (QueryTypeInterface $queryType): bool {
                return $queryType->isEnabled();
            }
        );
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->queryTypes);
    }

    public function count(): int
    {
        return count($this->queryTypes);
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->hasQueryType($offset);
    }

    /**
     * @param mixed $offset
     */
    public function offsetGet($offset): QueryTypeInterface
    {
        return $this->getQueryType($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new RuntimeException('Method call not supported.');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        throw new RuntimeException('Method call not supported.');
    }
}
