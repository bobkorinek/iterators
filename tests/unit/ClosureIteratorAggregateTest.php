<?php

/**
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace tests\loophp\iterators;

use loophp\iterators\ClosureIteratorAggregate;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversDefaultClass \loophp\iterators
 */
final class ClosureIteratorAggregateTest extends TestCase
{
    private const LIST_DATA = [1, 2, 3];

    private const MAP_DATA = ['foo' => 1, 'bar' => 2];

    public function testInitializeFromCallableWithArray(): void
    {
        $iterator = new ClosureIteratorAggregate(
            static fn (array $iterable): array => $iterable,
            [self::LIST_DATA]
        );

        self::assertTrue($iterator->getIterator()->valid());

        self::assertSame(
            self::LIST_DATA,
            iterator_to_array($iterator->getIterator())
        );
    }

    public function testRewind(): void
    {
        $iterator = new ClosureIteratorAggregate(
            static fn (array $iterable): array => $iterable,
            [self::MAP_DATA]
        );
        $newIterator = $iterator->getIterator();

        self::assertSame(1, $newIterator->current());
        $newIterator->next();
        self::assertSame(2, $newIterator->current());

        $newIterator = $iterator->getIterator();

        self::assertSame(1, $newIterator->current());
    }
}
