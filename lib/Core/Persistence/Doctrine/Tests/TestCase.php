<?php

namespace Netgen\BlockManager\Core\Persistence\Doctrine\Tests;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $databaseUri;

    /**
     * @var string
     */
    protected $databaseServer;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $databaseConnection;

    /**
     * Sets up the database connection.
     */
    protected function setUp()
    {
        $databaseUri = getenv('DATABASE');
        if (empty($databaseUri)) {
            $databaseUri = 'sqlite://:memory:';
            // $this->markTestSkipped('Database connection is needed to run this test');
        }

        $this->databaseUri = $databaseUri;

        preg_match('/^(?<db>.+):\/\//', $this->databaseUri, $matches);
        $this->databaseServer = $matches['db'];

        $this->createDatabaseConnection();
        $this->createDatabaseSchema();
        $this->insertDatabaseFixtures();
    }

    /**
     * Creates the database connection.
     */
    protected function createDatabaseConnection()
    {
        $this->databaseConnection = DriverManager::getConnection(
            array(
                'url' => $this->databaseUri,
            )
        );
    }

    /**
     * Creates the database schema.
     */
    protected function createDatabaseSchema()
    {
        $schema = file_get_contents(__DIR__ . '/_fixtures/schema/schema.' . $this->databaseServer . '.sql');
        $sqlQueries = explode(';', $schema);

        foreach ($sqlQueries as $sqlQuery) {
            if (!empty(trim($sqlQuery))) {
                $this->databaseConnection->query($sqlQuery);
            }
        }
    }

    /**
     * Inserts database fixtures.
     */
    protected function insertDatabaseFixtures()
    {
        $data = require __DIR__ . '/_fixtures/data.php';

        foreach ($data as $tableName => $tableData) {
            if (!empty($tableData)) {
                foreach ($tableData as $tableRow) {
                    $this->databaseConnection
                        ->createQueryBuilder()
                        ->insert($tableName)
                        ->values(array_fill_keys(array_keys($tableRow), '?'))
                        ->setParameters(
                            array_values($tableRow),
                            array_fill_keys(array_keys($tableRow), Type::STRING)
                        )
                        ->execute();
                }
            }
        }
    }
}
