# Simple Search PHP

[![Code Climate](https://codeclimate.com/github/markusos/simple-search-php/badges/gpa.svg)](https://codeclimate.com/github/markusos/simple-search-php)
[![Test Coverage](https://codeclimate.com/github/markusos/simple-search-php/badges/coverage.svg)](https://codeclimate.com/github/markusos/simple-search-php)
[![Build Status](https://travis-ci.org/markusos/simple-search-php.svg?branch=master)](https://travis-ci.org/markusos/simple-search-php)

A Simple Search Engine in PHP

- Query the document index using multi word queries.
- Search result is ranked with TF-IDF term weighting and cosine similarity.
- Uses MongoDB to persist the search index and all indexed documents.
- Uses Snowball stemming of tokens 

### Installation

To use the Search engine you fist need to install the necessary PHP extensions:

```pecl install mongo```

```pecl install stem```

When running the Search Engine make sure that the Mongod and Memcached processes are running on the server

### License

The MIT License (MIT)

Copyright (c) 2015 Markus Östberg
