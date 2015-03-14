#Simple Search

[![Code Climate](https://codeclimate.com/github/markusos/simple-search-php/badges/gpa.svg)](https://codeclimate.com/github/markusos/simple-search)
[![Test Coverage](https://codeclimate.com/github/markusos/simple-search-php/badges/coverage.svg)](https://codeclimate.com/github/markusos/simple-search)
[![Build Status](https://travis-ci.org/markusos/simple-search-php.svg?branch=master)](https://travis-ci.org/markusos/simple-search)

A Simple Search Engine in PHP

- Query the document index using multi word queries.
- Search result is ranked with TF-IDF term weighting and cosine similarity.
- Uses MongoDB to persist the search index and all indexed documents.
- Uses Snowball stemming of tokens.

**NOTE: this project is still in development**

###Install

``` bash
$ composer require markusos/simple-search dev-master
```

When running the Search Engine make sure that the MongoDB and Memcached processes are running on the server.

###System Requirements

You need **PHP >= 5.4.0** to use `markusos\simple-search` but the latest stable version of PHP is recommended.

If you want to use the MongoDB storage and/or index providers, you need to install the `mongo` extension.

``` bash
$ pecl install mongo
```

To use the snowball stemmer you also need to install the `stem` extension.

``` bash
$ pecl install stem
```

Make sure that you install the languages that you need when installing the `stem` extension.

###Usage

####Basic usage

**Index document:**

```php
<?php 

$document = new Search\Document(
    'Test Title',
    'This is a test document with some content to be searchable content'
    );

$engine = new Search\Engine();
$engine->addDocument($document);

```

**Search:**

```php
<?php 

$engine = new Search\Engine();
$result = $engine->search('test document');

```

####Custom setup

The search engine supports customization by exchangeable service providers. When initializing the search engine it is possible to switch out the default implementations and provide your own.

```php
<?php 

$config = new Config::createBuilder()
                ->tokenizer($tokenizer)
                ->store($store)
                ->index($index)
                ->ranker($ranker)
                ->stopWords($stopWords)
                ->build();
                
$engine = new Search\Engine($config);

```

Or you can use the default and override the settings you need:

```php
<?php 

$config = new Config::createBuilder()
                ->defaultConfig()
                ->stopWords(['new', 'list', 'of', 'stop', 'words'])
                ->build();
                
$engine = new Search\Engine($config);

```

###Testing

Simple Search has a [PHPUnit](https://phpunit.de/) test suite. To run the tests, run the following command from the project folder:

``` bash
$ phpunit
```

If you don't want to run the integration tests that require Memcached and MongoDB to be started, simply run: 

``` bash
$ phpunit --testsuite unit
```

###License

The MIT License (MIT)

Copyright (c) 2015 Markus Östberg
