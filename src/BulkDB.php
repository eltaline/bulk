<?php

declare(strict_types=1);

namespace PDOBulk\Db;

abstract class BulkDB
{

    private $pdo;

    protected $table;

    protected $ifields = [];
    protected $cfields = [];
    protected $ufields = [];
    protected $efields = [];
    protected $rfields = [];
    protected $style = [];

    protected $inumFields;
    protected $cnumFields;
    protected $enumFields;
    protected $rnumFields;

    private $operationsPerQuery = 0;
    private $preparedStatement;

    private $ibuffer = [];

    private $ibufferSize = 0;

    private $totalOperations = 0;

    private $affectedRows = 0;

    private $result;

    private $ccl;

    public function __construct(\PDO $pdo, int $operationsPerQuery, string $table, array $ifields = [], array $cfields = [], array $efields = [], array $rfields = [], array $style = [])
    {

	$this->ccl = get_class($this);

	if (($operationsPerQuery < 1) || (!is_int($operationsPerQuery))) {
	    throw new \InvalidArgumentException('The number of operations per query must be 1 or more and need to be integer');
	}

	if (!is_string($table)) {
	    throw new \InvalidArgumentException('The table name need to be string');
	}

	$table = $this->addQuotes($table);

	foreach ($ifields as $i => $value) {
	    $ifields[$i] = $this->addQuotes($value);
	}
	foreach ($cfields as $i => $value) {
	    $cfields[$i] = $this->addQuotes($value);
	}
	foreach ($rfields as $i => $value) {
	    $rfields[$i] = $this->addQuotes($value);
	}

	$inumFields = count($ifields);
	$cnumFields = count($cfields);
	$enumFields = count($efields);
	$rnumFields = count($rfields);

	if ($inumFields === 0) {
	    throw new \InvalidArgumentException('The field list is empty');
	}

	$this->pdo     = $pdo;
	$this->table   = $table;
	$this->ifields = $ifields;

	$this->inumFields = $inumFields;

	if ($cnumFields >= 1) {
	    $this->cfields = $cfields;
	}

	if ($enumFields >= 1) {

	    $regpat = '/[\+\-\*\/\|]/';
	    $delims = '\+\-\*\/\|';

	    if ($this->ccl === 'PDOBulk\Db\PSLInsUpd') {

		$iname = 'EXCLUDED.';
		$ename = '';

	    } elseif ($this->ccl === 'PDOBulk\Db\MSLInsUpd') {

		$iname = 'VALUES(';
		$ename = ')';

	    } else {

		throw new \InvalidArgumentException('Class not supported');

	    }

	    foreach ($efields as $efield) {

		if (preg_match($regpat, $efield)) {

		    $earray = preg_split('/([' . $delims . '])/', $efield, -1, PREG_SPLIT_DELIM_CAPTURE);

		    $efs = $this->addQuotes($earray[0]);
		    $ese = $this->addQuotes($earray[2]);

		    $eop = $earray[1];

		    if ($eop === '+') {

			$fill = '' . $efs . ' = ' . $iname . '' . $efs . '' . $ename . ' + ' . $table . '.' . $ese . '';
			$ufields[] = $fill;
			continue;

		    } elseif ($eop === '-') {

			$fill = '' . $efs . ' = ' . $iname . '' . $efs . '' . $ename . ' - ' . $table . '.' . $ese . '';
			$ufields[] = $fill;
			continue;

		    } elseif ($eop === '*') {

			$fill = '' . $efs . ' = ' . $iname . '' . $efs . '' . $ename . ' * ' . $table . '.' . $ese . '';
			$ufields[] = $fill;
			continue;

		    } elseif ($eop === '/') {

			$fill = '' . $efs . ' = ' . $iname . '' . $efs . '' . $ename . ' / ' . $table . '.' . $ese . '';
			$ufields[] = $fill;
			continue;

		    } elseif ($eop === '|') {

			$edl = $earray[4] ?? null;

			if (!is_null($edl)) {

			    $fill = '' . $efs . ' = CONCAT_WS(\'' . $edl . '\', ' . $iname . '' . $efs . '' . $ename . ', ' . $table . '.' . $ese . ')';
			    $ufields[] = $fill;
			    continue;

			} else {

			    throw new \InvalidArgumentException('The concatenation delimiter can not be empty and need to be legal same delimiter \';\'');

			}

		    } else {

			throw new \InvalidArgumentException('Mode not supported');

		    }

		}

		$fill = $this->addQuotes($efield) . " = {$iname}" . $this->addQuotes($efield) . "{$ename}";
		$ufields[] = $fill;

	    }

	    $this->ufields = $ufields;

	}

	if ($rnumFields >= 1) {
		$this->rfields = $rfields;
	}

	if (!empty($style)) {

	    $regpat = '/[\|]/';
	    $delims = '\|';

	    foreach ($style as $setting) {

		if (preg_match($regpat, $setting)) {

		    $sarray = preg_split('/([' . $delims . '])/', $setting);

		    if (!empty($sarray[0])) $this->fstyle = $sarray[0];
		    if (!empty($sarray[1])) $this->sstyle = $sarray[1];

		} else {

		    $this->fstyle = $style[0];

		}

	    }

	}

	$this->operationsPerQuery = $operationsPerQuery;

	$query = $this->getQuery($operationsPerQuery);
	$this->preparedStatement = $this->pdo->prepare($query);

    }

    public function queue(...$ivalues)
    {

	$icount = count($ivalues);

	if ($icount !== $this->inumFields) {
	    throw new \InvalidArgumentException(sprintf('The number of values (%u) does not match the field count (%u).', $icount, $this->inumFields));
	}

	foreach ($ivalues as $ivalue) {
	    $this->ibuffer[] = $ivalue;
	}

	$this->ibufferSize++;
	$this->totalOperations++;

	if ($this->ibufferSize !== $this->operationsPerQuery) {
	    return false;
	}

	$statement = $this->preparedStatement;
	$statement->execute($this->ibuffer);

	if ((!empty($this->fstyle)) && (empty($this->sstyle))) {
	    $result = $statement->fetchAll(constant($this->fstyle));
	} elseif ((!empty($this->fstyle)) && (!empty($this->sstyle))) {
	    $result = $statement->fetchAll(constant($this->fstyle)|constant($this->sstyle));
	}

	$this->affectedRows += $statement->rowCount();

	$this->ibuffer = [];
	$this->ibufferSize = 0;

	if (!empty($result)) {
	    return $result;
	} else {
	    return true;
	}

    }

    public function queuearray(array $ivalues = [])
    {

	$icount = count($ivalues);

	if ($icount !== $this->inumFields) {
	    throw new \InvalidArgumentException(sprintf('The number of values (%u) does not match the field count (%u).', $icount, $this->inumFields));
	}

	foreach ($ivalues as $ivalue) {
	    $this->ibuffer[] = $ivalue;
	}

	$this->ibufferSize++;
	$this->totalOperations++;

	if ($this->ibufferSize !== $this->operationsPerQuery) {
	    return false;
	}

	$statement = $this->preparedStatement;
	$statement->execute($this->ibuffer);

	if ((!empty($this->fstyle)) && (empty($this->sstyle))) {
	    $result = $statement->fetchAll(constant($this->fstyle));
	} elseif ((!empty($this->fstyle)) && (!empty($this->sstyle))) {
	    $result = $statement->fetchAll(constant($this->fstyle)|constant($this->sstyle));
	}

	$this->affectedRows += $statement->rowCount();

	$this->ibuffer = [];
	$this->ibufferSize = 0;

	if (!empty($result)) {
	    return $result;
	} else {
	    return true;
	}

    }

    public function flush()
    {

	if ($this->ibufferSize === 0) {
	    return;
	}

	$query = $this->getQuery($this->ibufferSize);
	$statement = $this->pdo->prepare($query);
	$statement->execute($this->ibuffer);

	if ((!empty($this->fstyle)) && (empty($this->sstyle))) {
	    $result = $statement->fetchAll(constant($this->fstyle));
	} elseif ((!empty($this->fstyle)) && (!empty($this->sstyle))) {
	    $result = $statement->fetchAll(constant($this->fstyle)|constant($this->sstyle));
	}

	$this->affectedRows += $statement->rowCount();

	$this->ibuffer = [];
	$this->ibufferSize = 0;

	if (!empty($result)) {
	    return $result;
	}

    }

    public function reset() : void
    {

	$this->affectedRows = 0;
	$this->totalOperations = 0;

    }

    public function resetbuf() : void
    {

	$this->ibuffer = [];
	$this->ibufferSize = 0;
    }

    public function resetall() : void
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

    protected function addQuotes(string $field): string
    {

	$escapeSymbol = '';

	if (preg_match('#PSL#', $this->ccl)) {
	    $escapeSymbol = '"';
	}

	if (strpos($field, ':IS_RAW') !== false) {
	    return str_replace(':IS_RAW', '', $field);
	}

	return "{$escapeSymbol}{$field}{$escapeSymbol}";

    }

    abstract protected function getQuery(int $numRecords) : string;

}
