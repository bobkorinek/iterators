<?php

/**
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace loophp\iterators;

use Generator;
use IteratorAggregate;

/**
 * @template TKey
 * @template T
 *
 * @implements IteratorAggregate<TKey, T>
 */
final class ClosureIteratorAggregate implements IteratorAggregate
{
    /**
     * @var callable(mixed ...$parameters): iterable<TKey, T>
     */
    private $callable;

    /**
     * @var iterable<int, mixed>
     */
    private iterable $parameters;

    /**
     * @param callable(mixed ...$parameters): iterable<TKey, T> $callable
     * @param iterable<int, mixed> $parameters
     */
    public function __construct(callable $callable, iterable $parameters = [])
    {
        $this->callable = $callable;
        $this->parameters = $parameters;
    }

    /**
     * @return Generator<TKey, T>
     */
    public function getIterator(): Generator
    {
        yield from ($this->callable)(...$this->parameters);
    }
}
