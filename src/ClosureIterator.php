<?php

/**
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace loophp\iterators;

use Generator;
use Iterator;
use ReturnTypeWillChange;

/**
 * @template TKey
 * @template T
 *
 * @implements Iterator<TKey, T>
 */
final class ClosureIterator implements Iterator
{
    /**
     * @var callable(mixed): iterable<TKey, T>
     */
    private $callable;

    /**
     * @var Generator<TKey, T, mixed, void>
     */
    private Generator $generator;

    /**
     * @var iterable<int, mixed>
     */
    private iterable $parameters;

    /**
     * @param callable(mixed): iterable<TKey, T> $callable
     * @param iterable<int, mixed> $parameters
     */
    public function __construct(callable $callable, iterable $parameters = [])
    {
        $this->callable = $callable;
        $this->parameters = $parameters;
        $this->generator = $this->getGenerator();
    }

    /**
     * @return T
     */
    #[ReturnTypeWillChange]
    public function current()
    {
        return $this->generator->current();
    }

    /**
     * @return TKey
     */
    #[ReturnTypeWillChange]
    public function key()
    {
        return $this->generator->key();
    }

    public function next(): void
    {
        $this->generator->next();
    }

    public function rewind(): void
    {
        $this->generator = $this->getGenerator();
    }

    public function valid(): bool
    {
        return $this->generator->valid();
    }

    /**
     * @return Generator<TKey, T, mixed, void>
     */
    private function getGenerator(): Generator
    {
        yield from ($this->callable)(...$this->parameters);
    }
}
