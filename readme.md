<p align="center"><img src="https://repository-images.githubusercontent.com/232873829/e2c45600-33b0-11ea-941f-ba319f2d3c52" width="500"/></p>
<p align="center">
<a href="https://github.styleci.io/repos/232873829"><img src="https://github.styleci.io/repos/232873829/shield?branch=master" /></a>
<a href="https://packagist.org/packages/samirzz/jldb"><img src="https://img.shields.io/packagist/php-v/samirzz/jldb?label=php&style=flat-square"/></a>
<a href="https://packagist.org/packages/samirzz/jldb"><img src="https://img.shields.io/github/contributors/mohamed-samir907/jldb?style=flat-square"/></a>
<a href="https://packagist.org/packages/samirzz/jldb"><img src="https://img.shields.io/badge/License-MIT-blue?style=flat-square"/></a>
</p>

# PHP JLDB (JSON Lite DB)
Simple and powerfull tool that allows to use json file like a database. It provides collection of methods that you can use like a database query builder.

## Installation

```json
composer require samirzz/jldb
```

## Usage

1. Create a config.php file like that

```php
<?php

return [

    /**
     * The default json file storage path that the user store the data on it.
     */
    'db_path' => __DIR__ . '/../storage',

    /**
     * Database name (json file name)
     */
    'db_name' => 'default.json'
];

```

2. include config file in your project and create new object from the class like that

```php
<?php
// index.php
require __DIR__ . '/vendor/autoload.php';

use Samirzz\JsonDB\JsonDB;

$config = include __DIR__ . '/config/jsondb.php';

$db = new JsonDB($config);


```

Now, you can use the method like that

```php
// index.php

/**
 * NOTE:
 * When you write the name of the table, if the table
 * not exists we will create it for you.
 * So don't worry about the creation of the table.
 */

/*
 | Create Record on the table
 |
 */

$data = [
    "name" => "Mohamed Samir",
    "email" => "gm.mohamedsamir@gmail.com",
    "github" => "mohamed-samir907"
];

// This will create record on users table 
// If the data array doesn't has a primary key
// we will add primary key on create method to the data
// array. The default primary key is 'id' if you need
// to change it, pass the name of primary key as second paramenter
$users = $db->table('users')->create($data); // primaryKey = id
$users = $db->table('users')->create($data, '_key'); //primary key = _key


/*
 | Update an Existing Record on the table
 |
 */

$data = [
    "name" => "Orange",
    "quantity" => 4,
    "price" => 10,
    "totalPrice" => 40
];

$products = $db->table('products')->update(27, $data);

// if the primary key not equal to 'id' then you can pass the prmary key as the following
$products = $db->table('products')->update(27, $data, '_key');

/*
 | Delete an Existing Record on the table
 |
 */

$db->table('users')->delete(12);

// OR: in case of primary key changed
$db->table('users')->delete(12, '_key');



/*
 |      Fetch the data
 |
 */

// Get all tables data
$database = $db->all();

// Get table data
$products = $db->table('products')->find(27);
$products = $db->table('products')->find(27, '_key');


// Get table data
$products = $db->table('products')->get();

// Get with where
$products = $db->table('products')
    ->where('name', '=', 'Orange')
    ->get();

$products = $db->table('products')
    ->where('name', '=', 'Orange')
    ->where('totalPrice', '>=', '10')
    ->get();

// Get the records Paginated
$products = $db->table('products')->paginate(20);

// Get last record on the table
$product = $db->table('products')->last();

// Get first record on the table
$product = $db->table('products')->first();

// Get count records on the table
$countProducts = $db->table('products')->count();

// Get count of column=value in the table
$countOrange = $db->table('products')->countOf("name", "Orange");

// If you love object style you can convert the array to object like that
// use toObject() helper function
$users = toObject($db->table('users')->get());

foreach ($users as $user) {
    echo $user->name;
}

```

## TODO
- add join, like, take, skip, groupBy, orderBy
- support functions like sum, avg, ... and allow the user to create his own function.
- select(...$columns)
- create prepare trait to check pendings and return the result of them.

- change the structure
    - create folder for each database
    - create json file for each table

- add encryption to the database
- add username, password for connect to the database.
- add Model for each table
- add schema class and save the tables schema in json file related to the database it self.
- add validation class for validate the type of the column.
- add relationships between tables.

- Add support to redis