<?php

declare(strict_types=1);

namespace PDOBulk\Db;

abstract class UNSOp
{

    private $pdo;

    protected $table;

    protected $ifields = [];
    protected $cfields = [];
    protected $ufields = [];
    protected $efields = [];

    protected $inumFields;
    protected $cnumFields;
    protected $unumFields;

    private $operationsPerQuery;
    private $preparedStatement;

    private $ibuffer = [];

    private $ibufferSize = 0;

    private $totalOperations = 0;

    private $affectedRows = 0;

    public function __construct(\PDO $pdo, int $operationsPerQuery, string $table, array $ifields ?? [], array $cfields ?? [], array $efields ?? [])
    {

        if (($operationsPerQuery < 1) || (!is_int($operationsPerQuery))) {
            throw new \InvalidArgumentException('The number of operations per query must be 1 or more and need to be integer');
        }

        if (!is_string($table)) {
            throw new \InvalidArgumentException('The table name need to be string');
        }

        $inumFields = count($ifields);
        $cnumFields = count($cfields);
        $unumFields = count($ufields);

        if ($inumFields === 0) {
            throw new \InvalidArgumentException('The field list is empty');
        }

        $this->pdo        = $pdo;
        $this->table      = $table;
        $this->ifields    = $ifields;

        $this->inumFields = $inumFields;

        if ($cnumFields >= 1) {
	    $this->cfields = $cfields;
	}

        if ($unumFields >= 1) {

	    foreach ($efields as $efield) {

		$fill = '' . $efield . ' = EXCLUDED.' . $efield . '';
		$ufields[] = $fill;

	    }

	    $this->ufields = $ufields;

	}

        $this->operationsPerQuery = $operationsPerQuery;

        $query = $this->getQuery($operationsPerQuery);
        $this->preparedStatement = $this->pdo->prepare($query);

    }

    public function queue(...$ivalues) : bool
    {

        $icount = count($ivalues);

        if ($icount !== $this->inumFields) {
            throw new \InvalidArgumentException(sprintf('The number of values (%u) does not match the field count (%u).', $icount, $this->numFields));
        }

        foreach ($ivalues as $ivalue) {
            $this->ibuffer[] = $ivalue;
        }

        $this->ibufferSize++;
        $this->totalOperations++;

        if ($this->ibufferSize !== $this->operationsPerQuery) {
            return false;
        }

        $this->preparedStatement->execute($this->ibuffer);
        $this->affectedRows += $this->preparedStatement->rowCount();

        $this->ibuffer = [];
        $this->ibufferSize = 0;

        return true;

    }

    public function flush() : void
    {

        if ($this->ibufferSize === 0) {
            return;
        }

        $query = $this->getQuery($this->ibufferSize);
        $statement = $this->pdo->prepare($query);
        $statement->execute($this->ibuffer);
        $this->affectedRows += $statement->rowCount();

        $this->ibuffer = [];
        $this->ibufferSize = 0;

    }

    public function reset() : void
    {

        $this->ibuffer = [];
        $this->ibufferSize = 0;
        $this->affectedRows = 0;
        $this->totalOperations = 0;

    }

    public function getTotalOperations() : int
    {

        return $this->totalOperations;

    }

    public function getFlushedOperations() : int
    {

        return $this->totalOperations - $this->ibufferSize;

    }

    public function getPendingOperations() : int
    {

        return $this->ibufferSize;

    }

    public function getAffectedRows() : int
    {

        return $this->affectedRows;

    }

    abstract protected function getQuery(int $numRecords) : string;

}
