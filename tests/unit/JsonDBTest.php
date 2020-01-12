<?php

use PHPUnit\Framework\TestCase;
use Samirzz\JsonDB\Exceptions\DuplicatedIdException;
use Samirzz\JsonDB\JsonDB;

class JsonDBTest extends TestCase
{
    private $config = [
        'db_path' => __DIR__.'/../../storage',
        'db_name' => 'database.json',
    ];

    private $db;

    protected function setUp(): void
    {
        $this->db = new JsonDB($this->config);
    }

    /** @test */
    public function testCanCreateNewInstanceFromTheClass()
    {
        $this->assertInstanceOf(JsonDB::class, $this->db);
    }

    /** @test */
    public function testCanGetFullDatabaseNamePath()
    {
        $this->assertIsString($this->db->getDB());
    }

    /** @test */
    public function testCanCreateFileIfNotExists()
    {
        $this->assertNull($this->db->createFileIfNotExists());
    }

    /** @test */
    public function testCanCreateTableIfNotExists()
    {
        $this->db->table('users');

        $this->assertNull($this->db->createTableIfNotExists());
    }

    /** @test */
    public function testCanGetAllTablesData()
    {
        $this->assertIsArray($this->db->all());
    }

    /** @test */
    public function testCanCreateRecord()
    {
        $data = $this->db->table('users')->create([
            'name'  => 'Mohamed Samir',
            'email' => 'gm.mohamedsamir@gmail.com',
        ]);

        $this->assertIsArray($data);
    }

    /** @test */
    public function testCanNotCreateRecordWithExistingId()
    {
        $this->expectException(DuplicatedIdException::class);

        $data = $this->db->table('users')->create([
            'id'    => 1,
            'name'  => 'Mohamed Samir',
            'email' => 'gm.mohamedsamir@gmail.com',
        ]);
    }

    /** @test */
    public function testCanCreateRecordWithAutoIncrementAfterLastIdEvenIfIdExists()
    {
        $lastId = $this->db->table('users')->getLastId();

        $data = [
            'id'    => ($lastId + 1),
            'name'  => 'Mohamed Samir',
            'email' => 'gm.mohamedsamir@gmail.com',
        ];

        $expected = $this->db->table('users')->create($data);

        $this->assertEquals($expected, $data);
    }

    /** @test */
    public function testCanUpdateExistingRecord()
    {
        $data = [
            'name'   => 'Mohamed Samir 2',
            'github' => 'github.com/mohamed-samir907',
        ];

        $expected = $this->db->table('users')->update(1, $data);

        $this->assertIsArray($expected);
    }

    /** @test */
    public function testCanNotUpdateNotExistingRecord()
    {
        $data = [
            'name'   => 'Mohamed Samir 2',
            'github' => 'github.com/mohamed-samir907',
        ];

        $expected = $this->db->table('users')->update(10000, $data);

        $this->assertNull($expected);
    }

    /** @test */
    public function testCanFindExistingRecord()
    {
        $expected = $this->db->table('users')->find(1);

        $this->assertIsArray($expected);
    }

    /** @test */
    public function testCanNotFindNotExistingRecord()
    {
        $expected = $this->db->table('users')->find(10000);

        $this->assertNull($expected);
    }

    /** @test */
    public function testCanDeleteExistingRecord()
    {
        $expected = $this->db->table('users')->delete($this->db->table('users')->getLastId());

        $this->assertTrue($expected);
    }

    /** @test */
    public function testCanNotDeleteNotExistingRecord()
    {
        $expected = $this->db->table('users')->delete(1000);

        $this->assertFalse($expected);
    }

    /** @test */
    public function testCanGetDataPaginated()
    {
        $expected = $this->db->table('users')->paginate(10, 1);

        $this->assertIsArray($expected);
    }

    /** @test */
    public function testCanGetLastRecord()
    {
        $expected = $this->db->table('users')->last();

        $this->assertIsArray($expected);
    }

    /** @test */
    public function testCanGetFirstRecord()
    {
        $expected = $this->db->table('users')->first();

        $this->assertIsArray($expected);
    }

    /** @test */
    public function testCanGetCountOfRecords()
    {
        $expected = $this->db->table('users')->count();

        $this->assertIsInt($expected);
    }

    /** @test */
    public function testCanGetCountOfColumnValue()
    {
        $expected = $this->db->table('users')->countOf('email', 'gm.mohamedsamir@gmail.com');

        $this->assertIsInt($expected);
    }

    /** @test */
    public function testCanCleanAllDataInTheDatabase()
    {
        $expected = $this->db->cleanDatabase();

        $this->assertEmpty($expected);
    }
}
