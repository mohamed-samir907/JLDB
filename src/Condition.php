<?php

namespace Samirzz\JsonDB;

use Exception;

trait Condition
{
    /** @var array */
    private $operations = [
        '=',
        '>',
        '<',
        '>=',
        '<=',
        '!=',
        '<>',
    ];

    /** @var string */
    private $whereColumn;

    /** @var string */
    private $whereOperation;

    /** @var string */
    private $whereValue;

    /**
     * Prepare where statement.
     *
     * @param string $column
     * @param string $value
     * @param string $operation
     *
     * @return $this
     */
    public function where(string $column, string $operation, string $value)
    {
        $this->whereColumn = $column;

        if (!in_array($operation, $this->operations)) {
            throw new Exception('Not supported operation');
        }

        $this->whereOperation = $operation;
        $this->whereValue = $value;

        $this->get();

        $this->executeWhere($this->records);

        return $this;
    }

    /**
     * Prepare where statement.
     *
     * @param array $records
     *
     * @return null|array
     */
    protected function executeWhere($records)
    {
        if ($this->whereColumn != null
            && $this->whereValue != null
            && $this->whereOperation != null
        ) {
            $this->records = array_filter($records, function ($record) {
                return $this->getOperation($record[$this->whereColumn], $this->whereValue);
            }, ARRAY_FILTER_USE_BOTH);
        }

        return null;
    }

    /**
     * Get the operation condition.
     *
     * @param string $column
     * @param string $value
     *
     * @return bool
     */
    protected function getOperation($column, $value)
    {
        switch ($this->whereOperation) {
            case '=':
                return $column == $value;
            case '>':
                return $column > $value;
            case '<':
                return $column < $value;
            case '>=':
                return $column >= $value;
            case '<=':
                return $column <= $value;
            case '!=':
            case '<>':
                return $column != $value;
        }
    }
}
