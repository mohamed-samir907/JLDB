<?php

namespace Samirzz\JsonDB;

trait Join
{
    /** @var string */
    private $joinTable;

    /**
     * Prepare join statement
     *
     * @param  string $table
     * @return $this
     */
    public function join(string $table)
    {
        $this->joinTable = $table;

        return $this;
    }
}