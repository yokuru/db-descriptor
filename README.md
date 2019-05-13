Database Descriptor
===

[![Build Status](https://travis-ci.org/yokuru/db-descriptor.svg?branch=master)](https://travis-ci.org/yokuru/db-descriptor)
[![Coverage Status](https://coveralls.io/repos/github/yokuru/db-descriptor/badge.svg?branch=master)](https://coveralls.io/github/yokuru/db-descriptor?branch=master)
[![MIT License](http://img.shields.io/badge/license-MIT-blue.svg?style=flat)](LICENSE)

A simple database schema descriptor that supports only MySQL.


## Installation

With [Composer](https://getcomposer.org/):

```
composer require yokuru/db-descriptor
```

## Usage

```php
$pdo = new \PDO('YOUR_DSN', 'YOUR_USERNAME', 'YOUR_PASSWORD');
$descriptor = new MySqlDescriptor($pdo)
$database = $descriptor->describeDatabase();
```


## License

MIT