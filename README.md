
> auto databade upgrade tool to reduce development database versioning task

## Features

* **Upgrade** - Database Upgrade

* **Easy** - Extremely easy to learn and use, friendly construction

## Requirement

PHP 5.3+ and PDO extension installed

## Get Started

### Install via composer

Add KDBV to composer.json configuration file.
```
$ composer require ganeshkandu/kdbv
```

And update the composer
```
$ composer update
```

## creating kdbv databade

```php

// If you installed via composer, just use this code to requrie autoloader on the top of your projects.
require 'vendor/autoload.php';

// Using kdbv namespace
namespace kanduganesh;

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

## getting mysql quries in array

```php

require 'vendor/autoload.php';

// Using kdbv namespace
namespace kanduganesh;

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

## Upgrading mysql database

```php

require 'vendor/autoload.php';

// Using kdbv namespace
namespace kanduganesh;

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