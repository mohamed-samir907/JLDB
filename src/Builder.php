<?php

namespace Samirzz\JsonDB;

trait Builder
{
    /**
     * Prepare everything pefore do an operation.
     *
     * @return string
     */
    public function prepare()
    {
        $this->createFileIfNotExists();
        $this->createTableIfNotExists();
    }

    /**
     * Create json file with the db name if the file not exists.
     *
     * @return void
     */
    public function createFileIfNotExists()
    {
        if (!file_exists($this->getDB())) {
            $table = json_encode([$this->table => []]);
            file_put_contents($this->getDB(), $table);
        }
    }

    /**
     * Create new table if not exists.
     *
     * @return void
     */
    public function createTableIfNotExists()
    {
        $database = $this->getFileData();

        if (null == $database || empty($database)) {
            $database[$this->table] = [];
        }

        if (!array_key_exists($this->table, $database)) {
            $database[$this->table] = [];
        }

        $this->latestDB = $database;
        $this->save();
    }

    /**
     * Get all data from the database.
     *
     * @return array
     */
    public function getFileData()
    {
        return json_decode(file_get_contents($this->getDB()), true);
    }

    /**
     * Save last edits on the database.
     *
     * @return void
     */
    public function save()
    {
        file_put_contents($this->getDB(), json_encode($this->latestDB));
    }

    /**
     * Rewrite table on the database.
     *
     * @param array $table
     *
     * @return void
     */
    public function rewriteDatabaseTable(array $table)
    {
        $database = $this->all();
        $database[$this->table] = array_values($table);
        $this->latestDB = $database;
        $this->save();
    }

    /**
     * Get the last id from table.
     *
     * @return int
     */
    public function getLastId()
    {
        $tableData = $this->get();

        $lastRecord = (count($tableData) > 0) ? end($tableData) : 0;

        if ($lastRecord !== false) {
            return $lastRecord['id'];
        }

        return $lastRecord;
    }

    /**
     * Get the record index if exists.
     *
     * @param string|int $id
     * @param string     $primaryKey
     *
     * @return bool|int
     */
    public function getRecordIndex($id, string $primaryKey = 'id')
    {
        return array_search($id, array_column($this->get(), $primaryKey));
    }

    /**
     * Delete all data in the database.
     *
     * @return void
     */
    public function cleanDatabase()
    {
        file_put_contents($this->getDB(), '');
    }
}
