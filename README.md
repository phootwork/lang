# phootwork/lang

[![License](https://img.shields.io/github/license/phootwork/lang.svg?style=flat-square)](https://packagist.org/packages/phootwork/lang)
[![Latest Stable Version](https://img.shields.io/packagist/v/phootwork/lang.svg?style=flat-square)](https://packagist.org/packages/phootwork/lang)
[![Total Downloads](https://img.shields.io/packagist/dt/phootwork/lang.svg?style=flat-square&colorB=007ec6)](https://packagist.org/packages/phootwork/lang)<br>
[![Build Status](https://img.shields.io/scrutinizer/build/g/phootwork/lang.svg?style=flat-square)](https://travis-ci.org/phootwork/lang)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/phootwork/lang.svg?style=flat-square)](https://scrutinizer-ci.com/g/phootwork/lang)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/phootwork/lang.svg?style=flat-square)](https://scrutinizer-ci.com/g/phootwork/lang)

Missing PHP language constructs

## Goals

- Provide common but missing php classes
- Objects for native php constructs
- Consistent API
- Inspired by `java.lang`, `java.util`, `String.prototype`, `Array.prototype` and [`Stringy`](https://github.com/danielstjules/Stringy)

## Installation

Installation via composer:

```
composer require phootwork/lang
```

## Documentation

[https://phootwork.github.io/lang](https://phootwork.github.io/lang)

## Running tests

This package is a part of the Phootwork library. In order to run the test suite, you have to download the full library.

```
git clone https://github.com/phootwork/phootwork
```
Then install the dependencies via composer:

```
composer install
```
Now, run the *lang* test suite:

```
vendor/bin/phpunit --testsuite lang
```
If you want to run the whole library tests, simply run:

```
vendor/bin/phpunit
```

## Contact

Report issues at the github [Issue Tracker](https://github.com/phootwork/lang/issues).

## Changelog

Refer to [Releases](https://github.com/phootwork/lang/releases)