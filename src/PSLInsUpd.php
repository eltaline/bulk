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
	$rfields = implode(', ', $this->rfields);

	$ivalues = implode(', ', array_fill(0, $this->inumFields, '?'));

	$query  = 'INSERT INTO ' . $this->table . ' (' . $ifields . ') VALUES (' . $ivalues . ')';

	if (empty($rfields)) {

	    $endquery = ' ON CONFLICT (' . $cfields . ') DO UPDATE SET ' . $ufields . '';

	} elseif (!empty($rfields)) {

	    $endquery = ' ON CONFLICT (' . $cfields . ') DO UPDATE SET ' . $ufields . ' RETURNING ' . $rfields . '';

	}

	$query .= str_repeat(', (' . $ivalues . ')', $numRecords - 1);
	$query = ''. $query . '' . $endquery . '';

	return $query;

    }

}
