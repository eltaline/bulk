<?php

declare(strict_types=1);

namespace PDOBulk\Db;

class BulkDel extends BulkOp
{

    protected function getQuery(int $numRecords) : string
    {

        $parts = [];

        foreach ($this->fields as $field) {
            $parts[] = $field . ' = ?';
        }

        $where = '(' . implode(' AND ', $parts) . ')';

        $query = 'DELETE FROM ' . $this->table . ' WHERE ' . $where;
        $query .= str_repeat(' OR ' . $where, $numRecords - 1);

        return $query;

    }

}
