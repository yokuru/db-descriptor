Database Descriptor
===

[![MIT License](http://img.shields.io/badge/license-MIT-blue.svg?style=flat)](LICENSE)

A simple database descriptor that supports only MySQL.


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