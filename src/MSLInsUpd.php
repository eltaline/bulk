<?php

declare(strict_types=1);

namespace PDOBulk\Db;

class MSLInsUpd extends BulkDB
{

    protected function getQuery(int $numRecords) : string
    {

        $ifields = implode(', ', $this->ifields);
        $ufields = implode(', ', $this->ufields);

        $ivalues = implode(', ', array_fill(0, $this->inumFields, '?'));

        $query  = 'INSERT INTO ' . $this->table . ' (' . $ifields . ') VALUES (' . $ivalues . ')';
	$endquery = ' ON DUPLICATE KEY UPDATE ' . $ufields . '';

        $query .= str_repeat(', (' . $ivalues . ')', $numRecords - 1);
	$query = ''. $query . '' . $endquery . '';

        return $query;

    }

}

