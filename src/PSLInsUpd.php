<?php

declare(strict_types=1);

namespace PDOBulk\Db;

class PSLInsUpd extends BulkDB
{

    protected function getQuery(int $numRecords) : string
    {

        $ifields = implode(', ', $this->ifields);
        $cfields = implode(', ', $this->cfields);
        $ufields = implode(', ', $this->ufields);

        $ivalues = implode(', ', array_fill(0, $this->inumFields, '?'));

        $query  = 'INSERT INTO ' . $this->table . ' (' . $ifields . ') VALUES (' . $ivalues . ')';
	$endquery = ' ON CONFLICT (' . $cfields . ') DO UPDATE SET ' . $ufields . '';

        $query .= str_repeat(', (' . $ivalues . ')', $numRecords - 1);
	$query = ''. $query . '' . $endquery . '';

        return $query;

    }

}

