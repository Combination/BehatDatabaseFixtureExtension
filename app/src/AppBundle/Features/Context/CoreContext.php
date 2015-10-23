<?php

namespace AppBundle\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class CoreContext implements Context, KernelAwareContext
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->container = $kernel->getContainer();
    }

    /**
     * @Given /^Truncated tables$/
     * @param TableNode $table
     */
    public function truncatedTables(TableNode $table)
    {
        foreach ($table->getTable() as $tableNames) {
            foreach ($tableNames as $tableName) {
                $this->clearTable($tableName);
            }
        }
    }

    /**
     * @Given /^There are records in table \'([^\']+)\'$/
     * @param string $tableName
     * @param TableNode $table
     */
    public function thereAreItemsInTable($tableName, TableNode $table)
    {
        /* @var $connection \Doctrine\DBAL\Connection */
        $connection = $this->container->get('doctrine')->getConnection();

        $fields = $table->getRow(0);
        $tableFields = '`' . implode('`, `', $fields) . '`';
        $tablePlaceholders = ':' . implode(', :', $fields);
        $sth = $connection->prepare(
            str_replace(
                ['__table', '__fields', '__placeholders'],
                [$tableName, $tableFields, $tablePlaceholders],
                '
                    INSERT INTO __table (__fields)
                    VALUES (__placeholders)
                '
            )
        );

        foreach ($table as $row) {
            $sth->execute($row);
        }
    }

    /**
     * @Given /^Records in \'([^\']+)\' where \'([^\']+)\', has data$/
     * @param $tableName
     * @param $where
     * @param TableNode $table
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     */
    public function thereAreRecordsInWhere($tableName, $where, TableNode $table)
    {
        /** @var Connection $connection */
        $connection = $this->container->get('doctrine')->getConnection();

        $exists = new ArrayCollection($connection->query("SELECT * FROM $tableName WHERE $where")->fetchAll());
        $criteria = new Criteria();
        $recordsMatching = $exists->matching($criteria);
        $record = $recordsMatching->first();
        $i = 1;
        foreach ($table as $row) {
            foreach($row as $key => $value) {
                \PHPUnit_Framework_Assert::assertArrayHasKey($key, $record, "Column '$key' doesn't exists in database");
                \PHPUnit_Framework_Assert::assertEquals($value, $record[$key], "Invalid value for '$key' in row $i");
            }
            ++$i;
        }
    }

    /**
     * @param string $table
     */
    private function clearTable($table)
    {
        /** @var Connection $connection */
        $connection = $this->container->get('doctrine')->getConnection();
        $connection->query("TRUNCATE TABLE $table");
    }
}
