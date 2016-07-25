# Url matcher

[![Build Status](https://img.shields.io/travis/weew/url-matcher.svg)](https://travis-ci.org/weew/url-matcher)
[![Code Quality](https://img.shields.io/scrutinizer/g/weew/url-matcher.svg)](https://scrutinizer-ci.com/g/weew/url-matcher)
[![Test Coverage](https://img.shields.io/coveralls/weew/url-matcher.svg)](https://coveralls.io/github/weew/url-matcher)
[![Version](https://img.shields.io/packagist/v/weew/url-matcher.svg)](https://packagist.org/packages/weew/url-matcher)
[![Licence](https://img.shields.io/packagist/l/weew/url-matcher.svg)](https://packagist.org/packages/weew/url-matcher)

## Table of contents

- [Installation](#installation)
- [Introduction](#introduction)
- [Usage](#usage)
- [Matching](#matching)
- [Parsing](#parsing)
- [Replacing](#replacing)

## Installation

`composer require weew/url-matcher`

## Introduction

This this simple matcher allows you to match url like paths against patterns with placeholders and even extract them.

## Usage

Creating a new matcher is very simple.

```php
$matcher = new UrlMatcher();
```

### Matching

Below is a very basic matching example.

```php
// true
$matcher->match('users/{id}', 'users/1');

// false
$matcher->match('users/{id}', 'users');
```

Placeholders can be optional by adding `?` at the end.

```php
// true
$matcher->match('users/{id?}', 'users/1');

// true
$matcher->match('users/{id?}', 'users');
```

Placeholders can have custom patterns.

```php
$matcher->addPattern('id', '[0-9]+');

// true
$matcher->match('users/{id}', 'users/1');

// false
$matcher->match('users/{id}', 'users/abc');
```

You can provide patterns inline.

```php
// true
$matcher->match('users/{id}', 'users/1', [
    'id' => '[0-9]+',
]);
```

Placeholders can be optional too.

```php
// true
$matcher->match('users/{id?}', 'users/1', [
    'id' => '[0-9]+',
]);

// true
$matcher->match('users/{id?}', 'users', [
    'id' => '[0-9]+',
]);
```

### Parsing

Extracting placeholders is very trivial too. The `parse` method always returns an instance of `IDictionary`.

```php
$dictionary = $matcher->parse('users/{id}', 'users/123');
// 123
$dictionary->get('id');

$dictionary = $matcher->parse('users/{id}', 'users');
// null
$dictionary->get('id');
```

Of course, placeholders can have custom patterns.

```php
$matcher->addPattern('id', '[0-9]+');
$dictionary = $matcher->parse('users/{id}', 'users/123');
// 123
$dictionary->get('id');

$dictionary = $matcher->parse('users/{id}', 'users/abc');
// null
$dictionary->get('id');
```

### Replacing

You can do the opposite of parsing by replacing placeholders with specific values.

```php
// api.service.com
$matcher->replace('{subdomain}.service.com', 'subdomain', 'api');

// api.service.com/v1
$matcher->replaceAll('{subdomain}.service.com/{version}', ['subdomain' => 'api', 'version' => 'v1']);
```
