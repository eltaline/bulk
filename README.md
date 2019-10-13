Bulk\Db Project
========

Tools for easy use Bulk Inserts with support ON CONFLICT and ON DUPLICATE KEY and simple math logic.

Contains helper classes for interacting with databases.

Currently supported PDO Postgresql/MySQL.

Installation
------------

Install from composer:

```bash
cd /path/to/yourproject
composer require eltaline/bulk 
```

Or install by local file:

```bash
cd /path/to/yourproject
wget -O composer.json https://raw.githubusercontent.com/eltaline/bulk/master/composer-local.json
composer install
```

Requirements
------------

This library requires PHP 7.1 or later.

Package overview
----------------

This package contains several helpers:

PgSQL: `PSLIns` ,`PSLInsNth` ,`PSLInsUpd`, `PSLDel`.
MySQL: `MSLIns` ,`MSLInsNth` ,`MSLInsUpd`, `MSLDel`.

These classes built on top of `PDO`, allow you to speed up database
rows insertion & deletion by performing multiple(bulk) operations per query, with this API.

### Testing table scheme

Create table for testing in PostgreSQL/MySQL.

```
CREATE TABLE tablename (id integer NOT NULL, name varchar(128), class varchar(128), age integer, height integer, weight integer, PRIMARY KEY(id,name));
```

### PSLIns & MSLIns

This class takes advantage of the bulk insert to empty/temp tables.

To use it, create a `PSLIns` or `MSLIns` instance with:

- your `PDO` connection object
- the name of your table
- the number of inserts to perform per bulk query
- the name of the columns to insert

#### Examples

#### PostgreSQL:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\PSLIns;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Define Logic

$ins = new PSLIns($pdo, 1000, 'tablename', ['id', 'name', 'class', 'age', 'height', 'weight']);

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark', 'Soldier' , 24, 185, 85);
$ins->Queue(2, 'Steve', 'Engineer', 36, 180, 75);
$ins->Queue(3, 'Clara', 'Sniper', 18, 175, 50);

//Bulk Write Complete Operation

$ins->flush();
```

#### MySQL:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\MSLIns;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Define Logic

$ins = new MSLIns($pdo, 1000, 'tablename', ['id', 'name', 'class', 'age', 'height', 'weight']);

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark', 'Soldier' , 24, 185, 85);
$ins->Queue(2, 'Steve', 'Engineer', 36, 180, 75);
$ins->Queue(3, 'Clara', 'Sniper', 18, 175, 50);

//Bulk Write Complete Operation

$ins->flush();
```

#### PostgreSQL with Transaction:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\PSLIns;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Define Logic

$ins = new PSLIns($pdo, 1000, 'tablename', ['id', 'name', 'class', 'age', 'height', 'weight']);

$pdo->beginTransaction();

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark', 'Soldier' , 24, 185, 85);
$ins->Queue(2, 'Steve', 'Engineer', 36, 180, 75);
$ins->Queue(3, 'Clara', 'Sniper', 18, 175, 50);

//Bulk Write Complete Operation

$ins->flush();

$pdo->commit();

```

#### MySQL with Transaction:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\MSLIns;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Define Logic

$ins = new MSLIns($pdo, 1000, 'tablename', ['id', 'name', 'class', 'age', 'height', 'weight']);

$pdo->beginTransaction();

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark', 'Soldier' , 24, 185, 85);
$ins->Queue(2, 'Steve', 'Engineer', 36, 180, 75);
$ins->Queue(3, 'Clara', 'Sniper', 18, 175, 50);

//Bulk Write Complete Operation

$ins->flush();

$pdo->commit();

```

### PSLInsNth & MSLInsNth

This class takes advantage of the bulk insert without duplicate key errors in tables.
Implements ON CONFLICT DO NOTHING and INSERT IGNORE.

To use it, create a `PSLInsNth` or `MSLInsNth` instance with:

- your `PDO` connection object
- the name of your table
- the number of inserts to perform per bulk query
- the name of the columns to insert

#### Examples

#### PostgreSQL:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\PSLInsNth;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Define Logic

$ins = new PSLInsNth($pdo, 1000, 'tablename', ['id', 'name', 'class', 'age', 'height', 'weight']);

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark', 'Soldier' , 24, 185, 85);
$ins->Queue(2, 'Steve', 'Engineer', 36, 180, 75);
$ins->Queue(3, 'Clara', 'Sniper', 18, 175, 50);

//Bulk Write Complete Operation

$ins->flush();
```

#### MySQL:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\MSLInsNth;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Define Logic

$ins = new MSLInsNth($pdo, 1000, 'tablename', ['id', 'name', 'class', 'age', 'height', 'weight']);

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark', 'Soldier' , 24, 185, 85);
$ins->Queue(2, 'Steve', 'Engineer', 36, 180, 75);
$ins->Queue(3, 'Clara', 'Sniper', 18, 175, 50);

//Bulk Write Complete Operation

$ins->flush();
```

#### PostgreSQL with Transaction:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\PSLInsNth;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Define Logic

$ins = new PSLInsNth($pdo, 1000, 'tablename', ['id', 'name', 'class', 'age', 'height', 'weight']);

$pdo->beginTransaction();

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark', 'Soldier' , 24, 185, 85);
$ins->Queue(2, 'Steve', 'Engineer', 36, 180, 75);
$ins->Queue(3, 'Clara', 'Sniper', 18, 175, 50);

//Bulk Write Complete Operation

$ins->flush();

$pdo->commit();

```

#### MySQL with Transaction:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\MSLInsNth;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Define Logic

$ins = new MSLInsNth($pdo, 1000, 'tablename', ['id', 'name', 'class', 'age', 'height', 'weight']);

$pdo->beginTransaction();

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark', 'Soldier' , 24, 185, 85);
$ins->Queue(2, 'Steve', 'Engineer', 36, 180, 75);
$ins->Queue(3, 'Clara', 'Sniper', 18, 175, 50);

//Bulk Write Complete Operation

$ins->flush();

$pdo->commit();

```

### PSLInsUpd & MSLInsUpd

This class takes advantage of the bulk insert with simple math logic in tables.
Implements ON CONFLICT (unqiue/composite key) DO UPDATE SET ... and ON DUPLICATE KEY UPDATE ... .

Supported + - / * operators (column+column, column-column, column/column , column*column).

Also implemented basic concatenations with separator (column|column|;) where ; is separator
for concatenation of existing value with new value delimiting by separator.

To use it, create a `PSLInsUpd` or `MSLInsUpd` instance with:

- your `PDO` connection object
- the name of your table
- the number of inserts to perform per bulk query
- the name of the columns to insert
- the name of the columns as primary key of table (unique/composite)
- the name of the columns for update or column names with format column+column for update and value addition

#### Examples

In this examples we are working with table contains composite primary key ['id', 'name'] and
with/without custom simple logic with addition new value to current value in for update columns.
Also shows example with concatenation.

Addition logic sometimes needed for statistical addition in fields.

#### PostgreSQL:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\PSLInsUpd;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Define Logic

$ins = new PSLInsUpd($pdo, 1000, 'tablename', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['height','weight']);

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark', 'Soldier' , 24, 185, 85);
$ins->Queue(2, 'Steve', 'Engineer', 36, 180, 75);
$ins->Queue(3, 'Clara', 'Sniper', 18, 175, 50);

//Bulk Write Complete Operation

$ins->flush();
```

#### MySQL:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\MSLInsUpd;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Define Logic

$ins = new MSLInsUpd($pdo, 1000, 'tablename', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['height','weight']);

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark', 'Soldier' , 24, 185, 85);
$ins->Queue(2, 'Steve', 'Engineer', 36, 180, 75);
$ins->Queue(3, 'Clara', 'Sniper', 18, 175, 50);

//Bulk Write Complete Operation

$ins->flush();
```

#### PostgreSQL with Transaction:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\PSLInsUpd;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Define Logic

$ins = new PSLInsUpd($pdo, 1000, 'tablename', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['height','weight']);

$pdo->beginTransaction();

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark', 'Soldier' , 24, 185, 85);
$ins->Queue(2, 'Steve', 'Engineer', 36, 180, 75);
$ins->Queue(3, 'Clara', 'Sniper', 18, 175, 50);

//Bulk Write Complete Operation

$ins->flush();

$pdo->commit();

```

#### MySQL with Transaction:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\MSLInsUpd;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Define Logic

$ins = new MSLInsUpd($pdo, 1000, 'tablename', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['height','weight']);

$pdo->beginTransaction();

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark', 'Soldier' , 24, 185, 85);
$ins->Queue(2, 'Steve', 'Engineer', 36, 180, 75);
$ins->Queue(3, 'Clara', 'Sniper', 18, 175, 50);

//Bulk Write Complete Operation

$ins->flush();

$pdo->commit();

```

### Addition Logic

#### PostgreSQL With Addition Logic:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\PSLInsUpd;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Define Logic

$ins = new PSLInsUpd($pdo, 1000, 'tablename', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['height+height','weight+weight']);

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark', 'Soldier' , 24, 185, 85);
$ins->Queue(2, 'Steve', 'Engineer', 36, 180, 75);
$ins->Queue(3, 'Clara', 'Sniper', 18, 175, 50);

//Bulk Write Complete Operation

$ins->flush();
```

#### MySQL With Addition Logic:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\MSLInsUpd;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Define Logic

$ins = new MSLInsUpd($pdo, 1000, 'tablename', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['height+height','weight+weight']);

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark', 'Soldier' , 24, 185, 85);
$ins->Queue(2, 'Steve', 'Engineer', 36, 180, 75);
$ins->Queue(3, 'Clara', 'Sniper', 18, 175, 50);

//Bulk Write Complete Operation

$ins->flush();
```

#### PostgreSQL with Transaction and Addition Logic:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\PSLInsUpd;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Define Logic

$ins = new PSLInsUpd($pdo, 1000, 'tablename', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['height+height','weight+weight']);

$pdo->beginTransaction();

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark', 'Soldier' , 24, 185, 85);
$ins->Queue(2, 'Steve', 'Engineer', 36, 180, 75);
$ins->Queue(3, 'Clara', 'Sniper', 18, 175, 50);

//Bulk Write Complete Operation

$ins->flush();

$pdo->commit();

```

#### MySQL with Transaction and Addition Logic:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\MSLInsUpd;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Define Logic

$ins = new MSLInsUpd($pdo, 1000, 'tablename', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['height+height','weight+weight']);

$pdo->beginTransaction();

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark', 'Soldier' , 24, 185, 85);
$ins->Queue(2, 'Steve', 'Engineer', 36, 180, 75);
$ins->Queue(3, 'Clara', 'Sniper', 18, 175, 50);

//Bulk Write Complete Operation

$ins->flush();

$pdo->commit();

```

### Concatenation Logic

#### PostgreSQL With Concatenation Logic:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\PSLInsUpd;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Define Logic

$ins = new PSLInsUpd($pdo, 1000, 'tablename', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['class|class|;']);

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark', 'Soldier' , 24, 185, 85);
$ins->Queue(2, 'Steve', 'Engineer', 36, 180, 75);
$ins->Queue(3, 'Clara', 'Sniper', 18, 175, 50);

//Bulk Write Complete Operation

$ins->flush();
```

#### MySQL With Concatenation Logic:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\MSLInsUpd;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Define Logic

$ins = new MSLInsUpd($pdo, 1000, 'tablename', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['class|class|;']);

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark', 'Soldier' , 24, 185, 85);
$ins->Queue(2, 'Steve', 'Engineer', 36, 180, 75);
$ins->Queue(3, 'Clara', 'Sniper', 18, 175, 50);

//Bulk Write Complete Operation

$ins->flush();
```

#### PostgreSQL with Transaction and Concatenation Logic:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\PSLInsUpd;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Define Logic

$ins = new PSLInsUpd($pdo, 1000, 'tablename', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['class|class|;']);

$pdo->beginTransaction();

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark', 'Soldier' , 24, 185, 85);
$ins->Queue(2, 'Steve', 'Engineer', 36, 180, 75);
$ins->Queue(3, 'Clara', 'Sniper', 18, 175, 50);

//Bulk Write Complete Operation

$ins->flush();

$pdo->commit();

```

#### MySQL with Transaction and Concatenation Logic:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\MSLInsUpd;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Define Logic

$ins = new MSLInsUpd($pdo, 1000, 'tablename', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['class|class|;']);

$pdo->beginTransaction();

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark', 'Soldier' , 24, 185, 85);
$ins->Queue(2, 'Steve', 'Engineer', 36, 180, 75);
$ins->Queue(3, 'Clara', 'Sniper', 18, 175, 50);

//Bulk Write Complete Operation

$ins->flush();

$pdo->commit();

```

### PSLDel & MSLDel

This class takes advantage of the bulk delete by value`s of column or columns.
Implements DELETE FROM.

To use it, create a `PSLDel` or `MSLDel` instance with:

- your `PDO` connection object
- the name of your table
- the number of deletes to perform per bulk query
- the name of the column or columns for where statement

#### Examples

#### PostgreSQL:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\PSLDel;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Define Logic

$ins = new PSLDel($pdo, 1000, 'tablename', ['id', 'name']);

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark');
$ins->Queue(2, 'Steve');
$ins->Queue(3, 'Clara');

//Bulk Write Complete Operation

$ins->flush();
```

#### MySQL:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\MSLDel;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$ins = new MSLDel($pdo, 1000, 'tablename', ['id', 'name']);

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark');
$ins->Queue(2, 'Steve');
$ins->Queue(3, 'Clara');

//Bulk Write Complete Operation

$ins->flush();
```

#### PostgreSQL with Transaction:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\PSLDel;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Define Logic

$ins = new PSLDel($pdo, 1000, 'tablename', ['id', 'name']);

$pdo->beginTransaction();

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark');
$ins->Queue(2, 'Steve');
$ins->Queue(3, 'Clara');

//Bulk Write Complete Operation

$ins->flush();

$pdo->commit();

```

#### MySQL with Transaction:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\MSLDel;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Define Logic

$ins = new MSLDel($pdo, 1000, 'tablename', ['id', 'name']);

$pdo->beginTransaction();

//Prepare Queries for Bulk Operation

$ins->Queue(1, 'Mark');
$ins->Queue(2, 'Steve');
$ins->Queue(3, 'Clara');

//Bulk Write Complete Operation

$ins->flush();

$pdo->commit();

```

### Performance tips

To get the maximum performance out of this library, you should:

- wrap your operations in a transaction
- disable emulation of prepared statements (`PDO::ATTR_EMULATE_PREPARES=false`)

These two tips combined can get you **up to 50% more throughput** in terms of inserts per second. Sample code:

### Recommendations

When using transactions, I recommend not forget use over this helpers - try and catch with throw and ```$pdo->rollBack();```.

### Limitations

Be careful when raising the number of operations per query, as you might hit these limits.

Recommend use this library with 100-50000 queries per bulk query insertions.

- PHP's [memory_limit]
- MySQL's [max_allowed_packet]
- MySQL also has a limit of 65535 placeholders per statement,
  effectively limiting the number of operations per query to `floor(65535 / number of columns)`.
  This does not apply if PDO emulates prepared statements.

### END
