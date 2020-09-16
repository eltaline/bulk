PDO Bulk Library
========

Tools for easy use Bulk Inserts with support ON CONFLICT/ON DUPLICATE KEY, RETURNING and simple math/concatenation logic.

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

PgSQL: `PSLIns`, `PSLInsNth`, `PSLInsUpd`, `PSLDel`.
MySQL: `MSLIns`, `MSLInsNth`, `MSLInsUpd`, `MSLDel`.

These classes built on top of `PDO`, allow you to speed up database
rows insertion & deletion by performing multiple(bulk) operations per query, with this API.

### Testing table scheme

Create table for testing in PostgreSQL/MySQL.

```
CREATE TABLE tablename (id integer NOT NULL, name varchar(128), class varchar(128), age integer, height integer, weight integer, PRIMARY KEY(id,name));
```

### Description important fucntions

**Flush function is important, and need to be used everywhere at the end of work with queue. Do write last queue buffer and reset queue buffer.**

```php
$ins->flush(); // Write last or < queue size, part of data from queue.
```

**Queue functions queue() or queuearray() can be used only separately in one loop.**

```php
$ins->queue(); // Collect data values until queue size, then write part of data.
$ins->queuearray(); // Collect data values(from simple array with single query values of data) until queue size, then write part of data.
```

### Description optional functions 

Can use optional counters in logic.

```php
$tot = $ins->getTotalOperations();
$aff = $ins->getAffectedRows();
```

Reset function for buffer and counters, do reset queue buffer and counters.

```php
$ins->reset(); // Resetting only counters
$ins->resetbuf(); // Resetting only buffer
$ins->resetall(); // Resetting buffer and counters
```

### PSLIns & MSLIns

This class takes advantage of the bulk insert to empty/temp tables.

- Implements INSERT ...
- Supported RETURNING operator

To use it, create a `PSLIns` or `MSLIns` instance with:

- your `PDO` connection object
- the number of inserts to perform per bulk query
- the name of your table
- the name of the columns to insert
- optional set empty array(not used for INSERT, but need empty if set RETURNING options)
- optional set empty array(not used for INSERT, but need empty if set RETURNING options)
- optional set RETURNING field or fields or '*'
- optional set PDO fetch mode for RETURNING clause(by default empty)

### PSLInsNth & MSLInsNth

This class takes advantage of the bulk insert without duplicate key errors in tables.

- Implements ON CONFLICT DO NOTHING and INSERT IGNORE
- Supported RETURNING operator

To use it, create a `PSLInsNth` or `MSLInsNth` instance with:

- your `PDO` connection object
- the number of inserts to perform per bulk query
- the name of your table
- the name of the columns to insert
- optional set empty array(not used for INSERT, but need empty if set RETURNING options)
- optional set empty array(not used for INSERT, but need empty if set RETURNING options)
- optional set RETURNING field or fields or '*'
- optional set PDO fetch mode for RETURNING clause(by default empty)

### PSLInsUpd & MSLInsUpd

This class takes advantage of the bulk insert with simple math logic in tables.

- Implements ON CONFLICT (unqiue/composite key) DO UPDATE SET ... and ON DUPLICATE KEY UPDATE ...
- Supported + - / * math operators (column+column, column-column, column/column , column*column)
- Supported | concatenation operator (column|column|;) where ; is separator
- Supported RETURNING operator

To use it, create a `PSLInsUpd` or `MSLInsUpd` instance with:

- your `PDO` connection object
- the number of inserts to perform per bulk query
- the name of your table
- the name of the columns to insert
- the name of the columns as primary key of table (unique/composite)
- the name of the columns for update or column names with format column+column for update and value addition
- optional set RETURNING field or fields or '*'
- optional set PDO fetch mode for RETURNING clause(by default empty)

### PSLDel & MSLDel

This class takes advantage of the bulk delete by value`s of column or columns.

- Implements DELETE ...
- Supported RETURNING operator

To use it, create a `PSLDel` or `MSLDel` instance with:

- your `PDO` connection object
- the number of inserts to perform per bulk query
- the name of your table
- the name of the column or columns for where statement
- optional set empty array(not using now for DELETE, but need empty if set RETURNING options)
- optional set empty array(not using now for DELETE, but need empty if set RETURNING options)
- optional set RETURNING field or fields or '*'
- optional set PDO fetch mode for RETURNING clause(by default empty)

### Beginning

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\PSLIns;
use PDOBulk\Db\PSLInsNth;
use PDOBulk\Db\PSLInsUpd;
use PDOBulk\Db\PSLDel;
use PDOBulk\Db\MSLIns;
use PDOBulk\Db\MSLInsNth;
use PDOBulk\Db\MSLInsUpd;
use PDOBulk\Db\MSLDel;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$data = array();

$data[] = ['1', 'Mark', 'Soldier' , '24', '175', '85'];
$data[] = ['2', 'Steve', 'Engineer', '36', '190', '95'];
$data[] = ['3', 'Clara', 'Sniper', '18', '180', '57'];
```

### Transactions

You can also use transaction for speed up bulk query.

```php
$pdo->beginTransaction(); // Before loop with queue() or queuearray();
$pdo->commit(); // After loop with queue() or queuearray();
```

Additionally use try with catch (Exception $e) for rollback transaction.

```php
$pdo->beginTransaction();

try {

    foreach (...) { 
	$ins->queue(...);
	//$ins->queuearray(...);
    }

} catch (Exception $e) {
	$pdo->rollBack();
	throw $ins;
    }

}

try {

    $ins->flush();
    $pdo->commit();

} catch (Exception $e) {

    $pdo->rollBack();
    throw $ins;

}
```

### Simple Operations

Supported in next classes:

PgSQL: `PSLIns`, `PSLInsNth`, `PSLInsUpd`, `PSLDel`.
MySQL: `MSLIns`, `PSLInsNth`, `MSLInsUpd`, `MSLDel`.

 - Classes PSLInsNth & MSLInsNth implements ON CONFLICT DO NOTHING & INSERT IGNORE clauses.

Format: (connection object, queue size, table, [values columns])

```php
$ins = new PSLIns($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight']);
$ins = new MSLIns($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight']);
```

Format: (connection object, queue size, table, [values columns])

```php
$ins = new PSLInsNth($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight']);
$ins = new MSLInsNth($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight']);
```

Format: (connection object, queue size, table, [values columns], [conflict/duplicate columns], [update columns])

```php
$ins = new PSLInsUpd($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['class','weight']);
$ins = new MSLInsUpd($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['class','weight']);
```

Format: (connection object, queue size, table, [values columns])

```php
$ins = new PSLDel($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight']);
$ins = new MSLDel($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight']);
```

#### Full example:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\PSLIns;
use PDOBulk\Db\PSLInsNth;
use PDOBulk\Db\PSLInsUpd;
use PDOBulk\Db\PSLDel;
use PDOBulk\Db\MSLIns;
use PDOBulk\Db\MSLInsNth;
use PDOBulk\Db\MSLInsUpd;
use PDOBulk\Db\MSLDel;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$data = array();

$data[] = ['1', 'Mark', 'Soldier' , '24', '175', '85'];
$data[] = ['2', 'Steve', 'Engineer', '36', '190', '95'];
$data[] = ['3', 'Clara', 'Sniper', '18', '180', '57'];

$ins = new PSLIns($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight']);
//$ins = new PSLInsNth($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight']);
//$ins = new PSLInsUpd($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['class','weight']);
//$ins = new PSLDel($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight']);
//$ins = new MSLIns($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight']);
//$ins = new MSLInsNth($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight']);
//$ins = new MSLInsUpd($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['class','weight']);
//$ins = new MSLDel($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight']);

foreach ($data as $fields) {

    $id = $fields[0];
    $name = $fields[1];
    $class = $fields[2];
    $age = $fields[3];
    $height = $fields[4];
    $weight = $fields[5];

    $ins->queue($id, $name, $class, $age, $height, $weight);
    //$ins->queuearray($fields);

}

//Bulk Write Complete Operation

$res = $ins->flush();

$tot = $ins->getTotalOperations();
$aff = $ins->getAffectedRows();

//Reset function for counters

$ins->reset();

print("Total queue operations: " . $tot . "\n");
print("Total affected rows: " . $aff . "\n");
```

### Advanced Operations

Supported in next classes:

PgSQL: `PSLInsUpd`.
MySQL: `MSLInsUpd`.

 - Supported math/concatenation operations on fields with previous values.

Format: (connection object, queue size, table, [values columns], [conflict/duplicate columns], [update columns])

```php
$ins = new PSLInsUpd($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['class|class|;','weight+weight']);
$ins = new MSLInsUpd($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['class|class|;','weight+weight']);

$ins = new PSLInsUpd($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['class|class|;','weight-weight']);
$ins = new MSLInsUpd($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['class|class|;','weight-weight']);

$ins = new PSLInsUpd($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['class|class|;','weight*weight']);
$ins = new MSLInsUpd($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['class|class|;','weight*weight']);

$ins = new PSLInsUpd($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['class|class|;','weight/weight']);
$ins = new MSLInsUpd($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['class|class|;','weight/weight']);
```

#### Full example:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\PSLInsUpd;
use PDOBulk\Db\MSLInsUpd;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$data = array();

$data[] = ['1', 'Mark', 'Soldier' , '24', '175', '85'];
$data[] = ['2', 'Steve', 'Engineer', '36', '190', '95'];
$data[] = ['3', 'Clara', 'Sniper', '18', '180', '57'];

$ins = new PSLInsUpd($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['class|class|;','weight+weight']);
//$ins = new MSLInsUpd($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['class|class|;','weight+weight']);

foreach ($data as $fields) {

    $id = $fields[0];
    $name = $fields[1];
    $class = $fields[2];
    $age = $fields[3];
    $height = $fields[4];
    $weight = $fields[5];

    $ins->queue($id, $name, $class, $age, $height, $weight);
    //$ins->queuearray($fields);

}

//Bulk Write Complete Operation

$res = $ins->flush();

$tot = $ins->getTotalOperations();
$aff = $ins->getAffectedRows();

//Reset function for counters

$ins->reset();

print("Total queue operations: " . $tot . "\n");
print("Total affected rows: " . $aff . "\n");
```

### Returning Operations

Supported in next classes:

PgSQL: `PSLIns`, `PSLInsUpd`, `PSLDel`.
MySQL: `MSLIns`, `MSLInsUpd`, `MSLDel`.

Use one of supported class with RETURNING operator.

 - Last argument is PDO fetch mode
 - Also can use advanced math/concatenation operations together with returning operations

Supported fetch modes:

```
PDO::FETCH_BOTH, PDO::FETCH_NUM,
PDO::FETCH_ASSOC, PDO::FETCH_UNIQUE,
PDO::FETCH_GROUP, PDO::FETCH_KEY_PAIR,
PDO::FETCH_GROUP|PDO::FETCH_COLUMN,
PDO::FETCH_OBJ
```

Format: (connection object, queue size, table, [values columns], [not used], [not used], [returning columns], [fetch mode])

```php
$ins = new PSLIns($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], [], [], ['id,name'], ['PDO::FETCH_ASSOC']);
$ins = new MSLIns($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], [], [], ['id,name'], ['PDO::FETCH_ASSOC']);
```

Format: (connection object, queue size, table, [values columns], [not used], [not used], [returning columns], [fetch mode])

```php
$ins = new PSLInsNth($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], [], [], ['id,name'], ['PDO::FETCH_ASSOC']);
$ins = new MSLInsNth($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], [], [], ['id,name'], ['PDO::FETCH_ASSOC']);
```

Format: (connection object, queue size, table, [values columns], [conflict/duplicate columns], [update columns], [returning columns], [fetch mode])

```php
$ins = new PSLInsUpd($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['class','weight'], ['id,name'], ['PDO::FETCH_ASSOC']);
$ins = new MSLInsUpd($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['class','weight'], ['id,name'], ['PDO::FETCH_ASSOC']);
```

Format: (connection object, queue size, table, [values columns], [not using now], [not using now], [returning columns], [fetch mode])

```php
$ins = new PSLDel($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], [], [], ['id,name'], ['PDO::FETCH_ASSOC']);
$ins = new MSLDel($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], [], [], ['id,name'], ['PDO::FETCH_ASSOC']);
```

#### Full example:

```php
include(__DIR__ . '/vendor/autoload.php');

use PDOBulk\Db\PSLIns;
use PDOBulk\Db\PSLInsUpd;
use PDOBulk\Db\PSLDel;
use PDOBulk\Db\MSLIns;
use PDOBulk\Db\MSLInsUpd;
use PDOBulk\Db\MSLDel;

$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$data = array();

$data[] = ['1', 'Mark', 'Soldier' , '24', '175', '85'];
$data[] = ['2', 'Steve', 'Engineer', '36', '190', '95'];
$data[] = ['3', 'Clara', 'Sniper', '18', '180', '57'];

$ins = new PSLIns($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], [], [], ['id,name'], ['PDO::FETCH_ASSOC']);
//$ins = new PSLInsUpd($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['class','weight'], ['id,name'], ['PDO::FETCH_ASSOC']);
//$ins = new PSLDel($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], [], [], ['id,name'], ['PDO::FETCH_ASSOC']);
//$ins = new MSLIns($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], [], [], ['id,name'], ['PDO::FETCH_ASSOC']);
//$ins = new MSLInsUpd($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], ['id','name'], ['class','weight'], ['id,name'], ['PDO::FETCH_ASSOC']);
//$ins = new MSLDel($pdo, 1000, 'users', ['id', 'name', 'class', 'age', 'height', 'weight'], [], [], ['id,name'], ['PDO::FETCH_ASSOC']);

$part = array(); // Define array for merge $res by RETURNING data of column or columns.

foreach ($data as $fields) {

    $id = $fields[0];
    $name = $fields[1];
    $class = $fields[2];
    $age = $fields[3];
    $height = $fields[4];
    $weight = $fields[5];

    $res = $ins->queue($id, $name, $class, $age, $height, $weight);
    //$res = $ins->queuearray($fields);

    if(!empty($res)) $part[] = $res;

}

//Bulk Write Complete Operation

$res = $ins->flush();

//Reassemble queue+flush part of RETURNING array

if(!empty($res)) $part[] = $res;

$retarray = array();

foreach($part as $k => $v) {
    foreach($v as $km => $vm) {
        $retarray[]=$vm;
    }
}

$tot = $ins->getTotalOperations();
$aff = $ins->getAffectedRows();

//Reset function for counters

$ins->reset();

print_r($retarray);

print("Total queue operations: " . $tot . "\n");
print("Total affected rows: " . $aff . "\n");
```

### Performance tips

To get the maximum performance out of this library, you should:

- wrap your operations in a transaction
- disable emulation of prepared statements (`PDO::ATTR_EMULATE_PREPARES=false`)

These two tips combined can get you **up to 50% more throughput** in terms of inserts per second.

### Recommendations

When using transactions, I recommend not forget use over this helpers - try and catch with throw and ```$pdo->rollBack();```.

### Limitations

Be careful when raising the number of operations per bulk query, as you might hit these limits.

$ins = new PSL...($pdo, **1000**, 'tablename', ['columnname']);

Recommended use this library with 100-1000 queries per bulk query insertions.

- PHP's [memory_limit]
- MySQL's [max_allowed_packet]
- PDO also has a limit of 65535 query parameters per statement,
  effectively limiting the number of operations per query to `floor(65535 / number of columns)`.

Maximum 65535 query parameters is allowed. Ex. 65535 / 10 columns ~= 10922 (is max queries per 1 bulk query).

### END
