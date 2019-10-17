<?php

declare(strict_types=1);

namespace PDOBulk\Db;

class MSLDel extends BulkDB
{

    protected function getQuery(int $numRecords) : string
    {

	$parts = [];

	$rfields = implode(', ', $this->rfields);

	foreach ($this->ifields as $ifield) {
	    $parts[] = $ifield . ' = ?';
	}

	$where = '(' . implode(' AND ', $parts) . ')';

	$query = 'DELETE FROM ' . $this->table . ' WHERE ' . $where;

	if (empty($rfields)) {

	    $endquery = '';

	} elseif (!empty($rfields)) {

	    $endquery = ' RETURNING ' . $rfields . '';

	}

	$query .= str_repeat(' OR ' . $where, $numRecords - 1);
	$query = ''. $query . '' . $endquery . '';

	return $query;

    }

}
