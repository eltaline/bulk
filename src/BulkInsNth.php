<?php

declare(strict_types=1);

namespace PDOBulk\Db;

class BulkInsNth extends BulkOp
{

    protected function getQuery(int $numRecords) : string
    {

        $ifields = implode(', ', $this->ifields);
        $ivalues = implode(', ', array_fill(0, $this->inumFields, '?'));

        $query  = 'INSERT INTO ' . $this->table . ' (' . $ifields . ') VALUES (' . $ivalues . ') ON CONFLICT DO NOTHING';
        $query .= str_repeat(', (' . $ivalues . ')', $numRecords - 1);

        return $query;

    }

}
