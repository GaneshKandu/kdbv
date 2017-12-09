
# Auto Upgrade tool
> upgrade your mysql database to current latest version from older version

#### Give star to libarary if you like **[STAR++](https://github.com/GaneshKandu/kdbv/star)**

## Features
* **Upgrade** - Database Upgrade
* **Easy** - Extremely easy to learn and use
* 
## Requirement

PHP 5.3+ and PDO extension installed

## Get Started

### Installation

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

#### or

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

### steps to perform
* Create ```kdbv database``` using ```make``` function of your ```latest database```
* deploy ```kdbv database``` with your application
* You can simply overwrite latest version of your application on your old version of application ( **NOTES** latest version is deployed with ```kdbv database``` and ```kdbv library``` ) 
* now you have your latest changed files with your old database which need to be update to new changes database structure
* now ```upgrade``` your database using ```upgrade``` function
* **ALL DONE ENJOY**
* if you getting any issue [create an issue](https://github.com/GaneshKandu/kdbv/issues)

### step 1

#### Instantiate & load()

```php
// Using kdbv namespace
namespace kanduganesh;
// just use this code to require auto loader on the top of your projects.
require 'vendor/autoload.php';
// Initialize
$obj = new kdbv(array(
	'HOST' => '<mysql_host>',
	'DATABASE' => '<mysql_database>',
	'USER' => '<database_user>',
	'PASS' => '<database_password>',
	'PORT' => '<mysql_port>',
	'KDBV' => '<kdbv_database_name>' //name of kdbv database
));
```
> ```<kdbv_database_name>``` is a name of ```kdbv database``` which to be deploy with your application
( _kdbv database contain database structure of your latest application_ )

### step 2
> use ```$obj``` of step 1
#### create ```kdbv database```

```
/*
Create kdbv database
notes :- during calling make function your mysql database should contain latest version database so it can store latest structure of database 
*/
$obj->make(); 
```
### step 3
> use ```$obj``` of step 1
#### Get Mysql Upgrade Queries
```php
$sqls_queries = $obj->query();
foreach($sqls_queries as $query){
    echo $query."\n";
}
```
## or
### Upgrade mysql database
```php
/*
upgrade mysql database
notes :- during calling upgrade function your kdbv database should be deployed with your application
Upgrade your old mysql database to your latest mysql database structure
*/
$obj->upgrade();
```
#### Maintainers

- [Ganesh Kandu](https://github.com/GaneshKandu)
	- [Google+](https://plus.google.com/u/0/+ganeshkandu)
	- [Linkedin](https://www.linkedin.com/in/ganesh-kandu-42b14373/)
	- [EMail](mailto:kanduganesh@gmail.com)
	- [Follow](https://github.com/GaneshKandu)

