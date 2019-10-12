<?php

declare(strict_types=1);

namespace PDOBulk\Db;

class PSLDel extends BulkDB
{

    protected function getQuery(int $numRecords) : string
    {

        $parts = [];

        foreach ($this->ifields as $ifield) {
            $parts[] = $ifield . ' = ?';
        }

        $where = '(' . implode(' AND ', $parts) . ')';

        $query = 'DELETE FROM ' . $this->table . ' WHERE ' . $where;
        $query .= str_repeat(' OR ' . $where, $numRecords - 1);

        return $query;

    }

}
