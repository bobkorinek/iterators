[![Latest Stable Version][latest stable version]][1]
[![GitHub stars][github stars]][1] [![Total Downloads][total downloads]][1]
[![GitHub Workflow Status][github workflow status]][2]
[![Type Coverage][type coverage]][4] [![License][license]][1]
[![Donate!][donate github]][5]

# PHP Iterators

The PHP Iterators library by [loophp](https://github.com/loophp) offers a
variety of useful classes to assist developers with iteration-related challenges
in PHP.

## Description

PHP provides a lot of functionality related to iterations. We can easily
implement our own [iterators](https://refactoring.guru/design-patterns/iterator)
within our code simply by implementing interfaces
like [`Iterator`](https://www.php.net/manual/en/class.iterator.php) or
[`IteratorAggregate`](https://www.php.net/manual/en/class.iteratoraggregate.php).
By implementing these interfaces, we can use the instances of these classes for
traversing the provided data. This is typically done
with [`foreach`](https://www.php.net/manual/en/control-structures.foreach.php)
statement. Iterators bring a lot of possibilities due to their nature. For
example, we can implement our own iterator class for parsing large amount of
data inside a file.
Unlike [array](https://www.php.net/manual/en/language.types.array.php) iterator
might load just one item at a time meaning we don't have to load the whole
content of the file into a memory. But unlike array if we decide, let's say,
reduce all the values inside our iterable into a single value (for array we
could simply
use [`array_reduce()`](https://www.php.net/manual/en/function.array-reduce.php)),
we won't find any help from PHP's native functions/classes.

This library helps to address the issue of the missing iterator functionality
inside PHP. You might
notice [PHP already has some of the classes for working with iterators](https://www.php.net/manual/en/spl.iterators.php)
but all these classes are specifically tied to `Iterator` interface, meaning you
can't use them for
other [iterables](https://www.php.net/manual/en/language.types.iterable.php) (
i.e. `array` and `IteratorAggregate`). With this library you can easily use some
the iterator-related logic for any `iterable` type.

Although the library provides many useful classes it might be a bit impractical
if you try use many of them at once or if you are dealing with a complex
situation requiring a lot of iterator related work. For this reason you might
consider using [`loophp/collection`](https://github.com/loophp/collection)
library.

## Installation

To use this library you can simply install it
with [Composer](https://getcomposer.org/):

```shell
composer require loophp/iterators
```

## Features

| Class                                                                               | Description                                                   |
|-------------------------------------------------------------------------------------|---------------------------------------------------------------|
| [`CachingIteratorAggregate`](#cachingiteratoraggregate)                             | Iterator that implements caching for iterators.               |
| [`ChunkIterableAggregate`](#chunkiterableaggregate)                                 | Iterator that chunks iterable items.                          |
| [`ClosureIterator`](#closureiterator)                                               | Iterator that uses a closure for iteration.                   |
| [`ClosureIteratorAggregate`](#closureiteratoraggregate)                             | Iterator for iterators using closures.                        |
| [`ConcatIterableAggregate`](#concatiterableaggregate)                               | Iterator that concatenates multiple iterables.                |
| [`FilterIterableAggregate`](#filteriterableaggregate)                               | Iterator that filters elements from an iterable.              |
| [`InterruptableIterableIteratorAggregate`](#interruptableiterableiteratoraggregate) | Iterator allowing interruption of iteration.                  |
| [`IterableIterator`](#iterableiterator)                                             | Base iterator that works with iterable objects.               |
| [`IterableIteratorAggregate`](#iterableiteratoraggregate)                           | Iterator for iterable iterators.                              |
| [`MapIterableAggregate`](#mapiterableaggregate)                                     | Iterator that maps functions over an iterable.                |
| [`MersenneTwisterRNGIteratorAggregate`](#mersennetwisterrngiteratoraggregate)       | Iterator providing Mersenne Twister random number generation. |
| [`MultipleIterableAggregate`](#multipleiterableaggregate)                           | Iterator that supports multiple iterable sources.             |
| [`PackIterableAggregate`](#packiterableaggregate)                                   | Iterator that packs elements from iterables.                  |
| [`PausableIteratorAggregate`](#pausableiteratoraggregate)                           | Iterator that allows pausing of iteration.                    |
| [`RandomIterableAggregate`](#randomiterableaggregate)                               | Iterator that provides random elements from iterables.        |
| [`RecursiveIterableAggregate`](#recursiveiterableaggregate)                         | Iterator that recursively iterates through nested iterables.  |
| [`ReductionIterableAggregate`](#reductioniterableaggregate)                         | Iterator that reduces iterables to a single value.            |
| [`ResourceIteratorAggregate`](#resourceiteratoraggregate)                           | Iterator for iterators based on resources.                    |
| [`SimpleCachingIteratorAggregate`](#simplecachingiteratoraggregate)                 | Basic caching mechanism for iterators.                        |
| [`SortIterableAggregate`](#sortiterableaggregate)                                   | Iterator that sorts elements of an iterable.                  |
| [`StringIteratorAggregate`](#stringiteratoraggregate)                               | Iterator that iterates over strings.                          |
| [`TypedIterableAggregate`](#typediterableaggregate)                                 | Iterator enforcing types for iterable elements.               |
| [`UniqueIterableAggregate`](#uniqueiterableaggregate)                               | Iterator that yields unique elements from an iterable.        |
| [`UnpackIterableAggregate`](#unpackiterableaggregate)                               | Iterator that unpacks elements from nested iterables.         |

## Why use iterators?

Many times we simple use `array` in our code to work with collections of values.
In this example we want to multiply all of the values inside the provided array
with chosen number.

```php
function multiply(array $numbers, int $value): array {
    return array_map(
        fn (int $number): int => $number * $value,
        $numbers
    );
}
```

This work well, but unfortunately this code is coupled to arrays even though
this type of operation (i.e. mapping collection of values) can be easily done
for any traversable value. If we want to support `iterable` type, we need to
rewrite this function.

```php
function multiply(iterable $numbers, int $value): array {
    return array_map(
        fn (int $number): int => $number * $value,
        iterator_to_array($numbers)
    );
}
```

Now this code works even if we pass `Iterator` or `IteratorAggregate` but there
is catch: this function converts _any_ `iterable` type into array. This might
not seem as a problem at first, but if we pass large set of numbers we might see
performance problems. There are two main problems here:

1. __We load all the values from the `iterable` variable into our memory__. If
   the variable contains an iterator accessing some large data sets we might
   lose a lot of memory due to this.
2. __There is unnecessary iteration over the `iterable` variable__. When
   calling `iterator_to_array()`, the function needs to iterate over
   the `iterable` variable to access the whole set of values to convert it into
   an array. If the user of the `multiply()` function decides to use the result
   to use it once simply for displaying all the number to console, there will 3
   iterations in total (first: `iterator_to_array`, second: `array_map`,
   third: `foreach` for displaying the numbers). The user might decide that this
   is a great burden for performance, so they might decide to simply rewrite the
   function within their `foreach` statement that displays the numbers.

## Usage

### CachingIteratorAggregate

Let you cache any iterator. You then get [\Generators][49] rewindable for free.

This implementation does not use internal state to keep track of the current
position of the iterator. The underlying mechanism is based
on [SPL \CachingIterator][48].

The pros of using that iterator is **performance**. It's blazing fast, it cannot
compare to any other stateful custom implementations.

This iterator will cache keys and values, of any type.

```php
<?php

// Generator
$generator = static function (): \Generator {
    yield true => 'foo';
    yield false => 'bar';
    yield ['foo', 'bar'] => 'foobar';
};

$iterator = new CachingIteratorAggregate($generator());

foreach ($iterator as $key => $value); // This will work.
foreach ($iterator as $key => $value); // This will also work.
```

### ChunkIterableAggregate

```php
<?php

$iterator = (new ChunkIterableAggregate(
    range('a', 'j'),
    2
));

foreach ($iterator as $chunk) {} // ['a', 'b'], ['c', 'd'], ...
```

### FilterIterableAggregate

```php
<?php

$iterator = (new FilterIterableAggregate(
    range(0, 5),
    static fn (int $v, int $key, iterable $iterable): bool =>
        0 === (($v + 2 * $key + count($iterable)) % 2)
));

foreach ($iterator as $filteredValue) {} // 0, 2, 4
```

### InterruptableIterableIteratorAggregate

Let you break the iterator at anytime.

Useful when working with infinite collection of items.

```php
<?php

// Generator
$naturals = static function () {
    $i = 0;

    while (true) {
        yield $i++;
    }
};

$iterator = new InterruptableIterableIteratorAggregate($generator());

foreach ($iterator as $generator => [$key, $value]) {
    var_dump($value);

    if (10 === $value) {
        $generator->send(InterruptableIterableIteratorAggregate::BREAK);
    }
}
```

### MapIterableAggregate

```php
<?php

$iterator = (new MapIterableAggregate(
    range('a', 'c'),
    static fn (string $letter, int $key, iterable $iterable): string =>
        sprintf(
            '%s::%s::%s',
            $key,
            $letter,
            gettype($iterable)
        )
));

foreach ($iterator as $tranformedValue) {}
```

### MersenneTwisterRNGIteratorAggregate

```php
<?php

$rngGenerator = (new MersenneTwisterRNGIteratorAggregate())
    ->withMin(1)
    ->withMax(10)
    ->withSeed($seed);

foreach ($rngGenerator as $randomValue) {} // Random integers in [1, 10]
```

### PackIterableAggregate

```php
<?php

// Generator
$generator = static function (): \Generator {
    yield true => 'foo';
    yield false => 'bar';
    yield ['foo', 'bar'] => 'foobar';
};

$iterator = new PackIterableAggregate($generator());

foreach ($iterator as $value);
/*
$value will yield the following values:

- [true, 'foo']
- [false, 'bar']
- [['foo', 'bar'], 'foobar']
*/
```

### UniqueIterableAggregate

```php
<?php

$generator = static function(): Generator {
    while (true) {
        yield mt_rand(0, 9);
    }
};

$iterator = new UniqueIterableAggregate($generator(), 1000);

foreach ($iterator as $value) {} // 9 random values only.
```

### UnpackIterableAggregate

```php
<?php

// Generator
$generator = static function (): \Generator {
    yield [true, 'foo'];
    yield [false, 'bar'];
    yield [['foo', 'bar'], 'foobar'];
};

$iterator = new UnpackIterableAggregate($generator());

foreach ($iterator as $key => $value);
/*
$key and $value will yield the following values:

- true => 'foo'
- false => 'bar'
- ['foo', 'bar'] => 'foobar'
*/
```

### ClosureIterator

```php
<?php

$callable = static fn (int $from, int $to) => yield from range($from, $to);

$iterator = new ClosureIterator($callable(10, 20));
```

### IterableIterator

```php
<?php

$iterator = new IterableIterator(range(1, 10));
```

### PausableIteratorAggregate

```php
<?php

$inputIterator = new ArrayIterator(range('a', 'e'));
$iteratorAggregate = new PausableIteratorAggregate($inputIterator);

$i = 0;
foreach ($iteratorAggregate as $v) {
    var_dump($v) // Print: 'a', 'b', 'c'
    if (++$i === 2) {
        break;
    }
}

foreach ($iteratorAggregate->rest() as $v) {
    var_dump($v) // Print: 'd', 'e'
}
```

### RandomIterableAggregate

In order to properly use this iterator, the user need to provide an extra
parameter `seed`. By default, this parameter is set to zero and thus, the
resulting iterator will be identical to the original one.

Random items are selected by choosing a random integer between zero and the
value of `seed`. If that value is zero, then the iterator will yield else it
will skip the value and start again with the next one.

The bigger the `seed` is, the bigger the entropy will be and the longer it will
take to yield random items. It's then up to the user to choose an appropriate
value. Usually a good value is twice the approximate amount of items the
decorated iterator has.

If you're willing to iterate multiple times on this, use the
`CachingIteratorAggregate` to cache the results.

This iterator works on keys and values, of any type.

```php
<?php

$seed = random_int(0, 1000);
$inputIterator = new ArrayIterator(range('a', 'e'));
$iterator = new RandomIterableAggregate($inputIterator, $seed);

foreach ($iterator as $v) {
    var_dump($v);
}

$iterator = new CachingIteratorAggregate(
    (new RandomIterableAggregate($inputIterator, $seed))->getIterator()
);

foreach ($iterator as $v) {
    var_dump($v);
}
foreach ($iterator as $v) {
    var_dump($v);
}
```

### RecursiveIterableAggregate

This iterator allows you to iterate through tree-like structures by simply
providing an `iterable` and callback to access its children.

```php
<?php

$treeStructure = [
    [
        'value' => '1',
        'children' => [
            [
                'value' => '1.1',
                'children' => [
                    [
                        'value' => '1.1.1',
                        'children' => [],
                    ],
                ],
            ],
            [
                'value' => '1.2',
                'children' => [],
            ],
        ],
    ],
    [
        'value' => '2',
        'children' => [],
    ],
];

$iterator = new RecursiveIterableAggregate(
    $treeStructure,
    fn (array $i) => $i['children']
);

foreach ($iterator as $item) {
    var_dump($item['value']); // This will print '1', '1.1', '1.1.1', '1.2', '2'
}
```

### ReductionIterableAggregate

```php
<?php

$iterator = (new ReductionIterableAggregate(
    range(0, 10),
    static fn (int $carry, int $value, int $key, iterable $iterable): int => $carry + $value,
    0
));

foreach ($iterator as $reduction) {} // [0, 1, 3, 6, 10, 15, 21, 28, 36, 45, 55]
```

### SortIterableAggregate

Implements a
[stable](https://en.m.wikipedia.org/wiki/Sorting_algorithm#Stability) sort
iterable aggregate

This means that if two elements have the same key, the one that appeared earlier
in the input will also appear earlier in the sorted output.

```php
$valueObjectFactory = static fn (int $id, int $weight): object => new class($id, $weight)
{
    public function __construct(public readonly int $id, public readonly int $weight) {}
};

$input = [
    $valueObjectFactory(id: 1, weight: 1),
    $valueObjectFactory(id: 2, weight: 1),
    $valueObjectFactory(id: 3, weight: 1),
];

$sort = new SortIterableAggregate(
  $input,
  static fn (object $a, object $b): int => $a->weight <=> $b->weight
);
```

## Code quality, tests, benchmarks

Every time changes are introduced into the library, [Github][2] runs the tests.

The library has tests written with [PHPUnit][35]. Feel free to check them out in
the `tests` directory.

Before each commit, some inspections are executed with [GrumPHP][36]; run
`composer grumphp` to check manually.

Static analyzers are also controlling the code. [PHPStan][38] and [PSalm][39]
are enabled to their maximum level.

## Contributing

Feel free to contribute by sending pull requests. We are a usually very
responsive team and we will help you going through your pull request from the
beginning to the end.

For some reasons, if you can't contribute to the code and willing to help,
sponsoring is a good, sound and safe way to show us some gratitude for the hours
we invested in this package.

Sponsor me on [Github][5] and/or any of [the contributors][6].

## Changelog

See [CHANGELOG.md][43] for a changelog based on [git commits][44].

For more detailed changelogs, please check [the release changelogs][45].

[1]: https://packagist.org/packages/loophp/iterators

[2]: https://github.com/loophp/iterators/actions

[4]: https://shepherd.dev/github/loophp/iterators

[5]: https://github.com/sponsors/drupol

[6]: https://github.com/loophp/iterators/graphs/contributors

[latest stable version]:
https://img.shields.io/packagist/v/loophp/iterators.svg?style=flat-square

[github stars]:
https://img.shields.io/github/stars/loophp/iterators.svg?style=flat-square

[total downloads]:
https://img.shields.io/packagist/dt/loophp/iterators.svg?style=flat-square

[github workflow status]:
https://img.shields.io/github/actions/workflow/status/loophp/iterators/tests.yml?branch=main&style=flat-square

[type coverage]:
https://img.shields.io/badge/dynamic/json?style=flat-square&color=color&label=Type%20coverage&query=message&url=https%3A%2F%2Fshepherd.dev%2Fgithub%2Floophp%2Fiterators%2Fcoverage

[license]:
https://img.shields.io/packagist/l/loophp/iterators.svg?style=flat-square

[donate github]:
https://img.shields.io/badge/Sponsor-Github-brightgreen.svg?style=flat-square

[donate paypal]:
https://img.shields.io/badge/Sponsor-Paypal-brightgreen.svg?style=flat-square

[34]: https://github.com/loophp/iterators/issues

[35]: https://www.phpunit.de/

[36]: https://github.com/phpro/grumphp

[38]: https://github.com/phpstan/phpstan

[39]: https://github.com/vimeo/psalm

[43]: https://github.com/loophp/iterators/blob/main/CHANGELOG.md

[44]: https://github.com/loophp/iterators/commits/main

[45]: https://github.com/loophp/iterators/releases

[48]: https://www.php.net/cachingiterator

[49]: https://www.php.net/generator
