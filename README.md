[![Latest Stable Version][latest stable version]][1]
 [![GitHub stars][github stars]][1]
 [![Total Downloads][total downloads]][1]
 [![GitHub Workflow Status][github workflow status]][2]
 [![Scrutinizer code quality][code quality]][3]
 [![Type Coverage][type coverage]][4]
 [![Code Coverage][code coverage]][3]
 [![Mutation testing badge][mutation badge url]][mutation badge link]
 [![License][license]][1]
 [![Donate!][donate github]][5]

# PHP Iterators

## Description

The missing PHP iterators.

## Features

* `CachingIteratorAggregate`
* `ClosureIterator`: `ClosureIterator(callable $callable, array $arguments = [])`
* `ClosureIteratorAggregate`: `ClosureIteratorAggregate(callable $callable, array $arguments = [])`
* `IterableIterator`: `IterableIterator(iterable $iterable)`
* `IterableIteratorAggregate`: `IterableIteratorAggregate(iterable $iterable)`
* `PackIterableAggregate`
* `UnpackIterableAggregate`
* `PausableIteratorAggregate`

## Installation

```composer require loophp/iterators```

## Usage

### CachingIteratorAggregate

Let you cache any iterator. You then get [\Generators][49]
rewindable for free.

This implementation does not use internal state to keep track
of the current position of the iterator.
The underlying mechanism is based on [SPL \CachingIterator][48].

The pros of using that iterator is **performance**. It's blazing fast,
it cannot compare to any other stateful custom implementations.

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

$iterator = new PausableIteratorAggregate(range('a', 'e'));

$i = 0;
foreach ($iterator as $v) {
    var_dump($v) // Print: 'a', 'b', 'c'
    if (++$i === 2) {
        break;
    }
}

foreach ($iterator->rest() as $v) {
    var_dump($v) // Print: 'd', 'e'
}
```

## Documentation

## Code quality, tests, benchmarks

Every time changes are introduced into the library, [Github][2] runs the
tests.

The library has tests written with [PHPSpec][35].
Feel free to check them out in the `spec` directory. Run `composer phpspec` to
trigger the tests.

Before each commit, some inspections are executed with [GrumPHP][36]; run
`composer grumphp` to check manually.

The quality of the tests is tested with [Infection][37] a PHP Mutation testing
framework - run `composer infection` to try it.

Static analyzers are also controlling the code. [PHPStan][38] and
[PSalm][39] are enabled to their maximum level.

## Contributing

Feel free to contribute by sending Github pull requests. I'm quite responsive :-)

If you can't contribute to the code, you can also sponsor me on [Github][5].

## Changelog

See [CHANGELOG.md][43] for a changelog based on [git commits][44].

For more detailed changelogs, please check [the release changelogs][45].

[1]: https://packagist.org/packages/loophp/iterators
[latest stable version]: https://img.shields.io/packagist/v/loophp/iterators.svg?style=flat-square
[github stars]: https://img.shields.io/github/stars/loophp/iterators.svg?style=flat-square
[total downloads]: https://img.shields.io/packagist/dt/loophp/iterators.svg?style=flat-square
[github workflow status]: https://img.shields.io/github/workflow/status/loophp/iterators/Unit%20tests?style=flat-square
[code quality]: https://img.shields.io/scrutinizer/quality/g/loophp/iterators/main.svg?style=flat-square
[3]: https://scrutinizer-ci.com/g/loophp/iterators/?branch=main
[type coverage]: https://img.shields.io/badge/dynamic/json?style=flat-square&color=color&label=Type%20coverage&query=message&url=https%3A%2F%2Fshepherd.dev%2Fgithub%2Floophp%2Fiterators%2Fcoverage
[4]: https://shepherd.dev/github/loophp/iterators
[code coverage]: https://img.shields.io/scrutinizer/coverage/g/loophp/iterators/main.svg?style=flat-square
[license]: https://img.shields.io/packagist/l/loophp/iterators.svg?style=flat-square
[donate github]: https://img.shields.io/badge/Sponsor-Github-brightgreen.svg?style=flat-square
[donate paypal]: https://img.shields.io/badge/Sponsor-Paypal-brightgreen.svg?style=flat-square
[mutation badge url]: https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Floophp%2Fiterators%2Fmain
[mutation badge link]: https://dashboard.stryker-mutator.io/reports/github.com/loophp/iterators/main
[7]: https://packagist.org/?query=iterators
[11]: https://en.wikipedia.org/wiki/Immutable_object
[12]: https://www.php.net/manual/en/class.generator.php
[13]: https://www.php.net/manual/en/class.iterator.php
[14]: https://en.wikipedia.org/wiki/SOLID
[15]: https://en.wikipedia.org/wiki/Pure_function
[16]: https://github.com/loophp/iterators/blob/main/src/iterators.php
[8]: https://www.php.net/array-map
[9]: https://www.php.net/array-filter
[10]: https://www.php.net/array-reduce
[17]: https://www.php.net/manual/en/class.splobjectstorage.php
[18]: https://loophp-iterators.readthedocs.io/en/stable/pages/usage.html#working-with-keys-and-values
[19]: https://github.com/illuminate/support
[20]: https://github.com/DusanKasan/Knapsack
[21]: https://github.com/mtdowling/transducers.php
[22]: https://ruby-doc.org/core-2.7.0/Array.html
[23]: https://collect.js.org/
[24]: https://github.com/nikic/iter
[27]: http://danieltao.com/lazy.js/
[33]: https://loophp-iterators.rtfd.io
[28]: https://loophp-iterators.readthedocs.io/en/stable/pages/api.html
[32]: https://loophp-iterators.readthedocs.io/en/stable/pages/usage.html
[34]: https://github.com/loophp/iterators/issues
[2]: https://github.com/loophp/iterators/actions
[35]: http://www.phpspec.net/
[36]: https://github.com/phpro/grumphp
[37]: https://github.com/infection/infection
[38]: https://github.com/phpstan/phpstan
[39]: https://github.com/vimeo/psalm
[5]: https://github.com/sponsors/drupol
[40]: https://www.reddit.com/r/PHP/comments/csxw23/a_stateless_and_modular_iterators_class/
[41]: https://www.reddit.com/r/PHP/comments/i2u2le/release_of_version_200_of_loophpiterators/
[42]: https://blog.jetbrains.com/phpstorm/2020/08/php-annotated-august-2020/
[43]: https://github.com/loophp/iterators/blob/main/CHANGELOG.md
[44]: https://github.com/loophp/iterators/commits/main
[45]: https://github.com/loophp/iterators/releases
[25]: https://www.haskell.org/
[29]: https://www.youtube.com/watch?v=m3svKOdZijA
[30]: http://hughfdjackson.com/javascript/why-curry-helps/
[26]: https://ramdajs.com/
[31]: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/flatMap
[46]: https://www.youtube.com/watch?v=Kp47f8dtqoo
[47]: https://loophp-iterators.readthedocs.io/en/stable/pages/principles.html
[48]: https://www.php.net/cachingiterator
[49]: https://www.php.net/generator