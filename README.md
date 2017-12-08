
> auto databade upgrade tool to reduce development database versioning task

## Features

* **Upgrade** - Database Upgrade

* **Easy** - Extremely easy to learn and use, friendly construction

## Requirement

PHP 5.3+ and PDO extension installed

## Get Started

## Install

This library is designed to be installed via [Composer](https://getcomposer.org/doc/).

Add the dependency into your projects composer.json.
```
{
  "require": {
    "ganeshkandu/kdbv": "*"
  }
}
```

Download the composer.phar
``` bash
curl -sS https://getcomposer.org/installer | php
```

Install the library.
``` bash
php composer.phar install
```

## or

> To add in in your dependencies

``` bash
php composer.phar require ganeshkandu/kdbv
```

## Autoloading

This library requires an autoloader, if you aren't already using one you can include [Composers autoloader](https://getcomposer.org/doc/01-basic-usage.md#autoloading).

``` php
require('vendor/autoload.php');
```

## Usage

### create kdbv databade

```php

// Using kdbv namespace
namespace kanduganesh;

// If you installed via composer, just use this code to require auto loader on the top of your projects.
require 'vendor/autoload.php';

// Initialize

/*
<host> database hostname
<database> database name
<user> database user
<password> database password
<port> database port
kdbv database stores database structure of latest database
<kdbvdb> database kdbv database
*/

$obj = new kdbv(array(
	'HOST' => '<host>',
	'DATABASE' => '<database>',
	'USER' => '<user>',
	'PASS' => '<password>',
	'PORT' => '<port>',
	'KDBV' => '<kdbvdb>',
));

// Enjoy
/*
Create kdbv database
*/
$obj->make();

```

### get mysql queries in array

```php

// Using kdbv namespace
namespace kanduganesh;

require 'vendor/autoload.php';

// Initialize

/*
<host> database hostname
<database> database name
<user> database user
<password> database password
<port> database port
kdbv database stores database structure of latest database
<kdbvdb> database kdbv database
*/

$obj = new kdbv(array(
	'HOST' => '<host>',
	'DATABASE' => '<database>',
	'USER' => '<user>',
	'PASS' => '<password>',
	'PORT' => '<port>',
	'KDBV' => '<kdbvdb>',
));
/*
get array of mysql upgrade queries
*/

$sql = $obj->query();

```

### Upgrade mysql database

```php

// Using kdbv namespace
namespace kanduganesh;

require 'vendor/autoload.php';

// Initialize

/*
<host> database hostname
<database> database name
<user> database user
<password> database password
<port> database port
kdbv database stores database structure of latest database
<kdbvdb> database kdbv database
*/

$obj = new kdbv(array(
	'HOST' => '<host>',
	'DATABASE' => '<database>',
	'USER' => '<user>',
	'PASS' => '<password>',
	'PORT' => '<port>',
	'KDBV' => '<kdbvdb>',
));

/*
upgrading database
*/

$obj->upgrade();

```
#### Maintainers

- [Ganesh Kandu](https://github.com/GaneshKandu)
	- [Google+](https://plus.google.com/u/0/+ganeshkandu)
	- [Linkedin](https://www.linkedin.com/in/ganesh-kandu-42b14373/)

