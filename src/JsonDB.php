<?php

namespace Samirzz\JsonDB;

use Samirzz\JsonDB\Exceptions\DuplicatedIdException;

class JsonDB
{
    use Builder;
    use Condition;
    /**
     * The Storage Path that the json file exists on it.
     *
     * @var array
     */
    private $dbPath;

    /**
     * The json file name.
     *
     * @var string
     */
    private $dbName;

    /**
     * The node that we need to create|read|update|delete on it.
     *
     * @var string
     */
    private $table;

    /**
     * The database after last edit, Used to put the data on it.
     *
     * @var array
     */
    private $latestDB;

    /**
     * The data the fetched from the table.
     *
     * @var array
     */
    private $records;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->dbPath = $config['db_path'];
        $this->dbName = $config['db_name'];
    }

    /**
     * Get the full path of the database name.
     *
     * @return string
     */
    public function getDB()
    {
        return rtrim($this->dbPath, '/').'/'.ltrim($this->dbName, '/');
    }

    /**
     * Node on the json file.
     *
     * @param string $table
     *
     * @return $this
     */
    public function table(string $table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Get all data from the database.
     *
     * @return array
     */
    public function all()
    {
        return $this->getFileData();
    }

    /**
     * Get the data from table.
     *
     * @return null|array
     */
    public function get()
    {
        $this->prepare();

        if ($this->records == null) {
            $this->records = $this->getFileData()[$this->table];
        }

        return $this->records;
    }

    /**
     * Store new record.
     *
     * @param mixed $data
     *
     * @return bool
     */
    public function create(array $data, $primaryKey = 'id')
    {
        if ($exists = array_key_exists($primaryKey, $data)
            && $this->find($data[$primaryKey]) != null
        ) {
            throw new DuplicatedIdException('Duplicated ID');
        }

        if (!$exists) {
            $data[$primaryKey] = ($this->getLastId() + 1);
        }

        $table = $this->get();
        $table[] = $data;

        $this->rewriteDatabaseTable($table);

        return $data;
    }

    /**
     * Update an existing record.
     *
     * @param string|int $id
     * @param array      $data
     * @param string     $primaryKey
     *
     * @return null|array
     */
    public function update($id, array $data, $primaryKey = 'id')
    {
        $index = $this->getRecordIndex($id, $primaryKey);

        if ($index === false) {
            return null;
        }

        $table = $this->get();
        $record = $table[$index];

        foreach ($data as $key => $value) {
            if ($key != $primaryKey) {
                $record[$key] = $value;
            }
        }

        $table[$index] = $record;

        $this->rewriteDatabaseTable($table);

        return $table[$index];
    }

    /**
     * Find a record on the table.
     *
     * @param string|int $id
     * @param string     $primaryKey
     *
     * @return null|array
     */
    public function find($id, string $primaryKey = 'id')
    {
        $index = $this->getRecordIndex($id, $primaryKey);

        if ($index !== false) {
            return $this->get()[$index];
        }

        return null;
    }

    /**
     * Delete existing record.
     *
     * @param string|int $id
     * @param string     $primaryKey
     *
     * @return bool
     */
    public function delete($id, $primaryKey = 'id')
    {
        $index = $this->getRecordIndex($id, $primaryKey);

        if ($index === false) {
            return false;
        }

        $table = $this->get();
        unset($table[$index]);

        $this->rewriteDatabaseTable($table);

        return true;
    }

    /**
     * Paginate the records.
     *
     * @param int $size
     *
     * @return array
     */
    public function paginate(int $size, $page = 1)
    {
        $page = intval($page);

        $data = array_chunk($this->get(), $size)[$page - 1];

        return [
            'data'   => $data,
            'schema' => [
                'current_page'  => $page,
                'next_page'     => ($page + 1),
                'previous_page' => ($page == 1) ? null : ($page - 1),
            ],
        ];
    }

    /**
     * Get the last record on the table.
     *
     * @return mixed
     */
    public function last()
    {
        $data = $this->get();

        return end($data);
    }

    /**
     * Get the last record on the table.
     *
     * @return mixed
     */
    public function first()
    {
        return $this->get()[0];
    }

    /**
     * Get the count of records on the table.
     *
     * @return int
     */
    public function count()
    {
        return count($this->get());
    }

    /**
     * Get the count of column in the table.
     *
     * @param string $column
     *
     * @return int
     */
    public function countOf($column, $value)
    {
        $arr = array_column($this->get(), $column);

        return array_count_values($arr)[$value];
    }
}
