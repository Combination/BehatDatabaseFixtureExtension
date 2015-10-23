<?php

namespace AppBundle\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
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
     * @Given /^There are items in table \'([^\']+)\'$/
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
     * @param string $table
     */
    private function clearTable($table)
    {
        /** @var Connection $connection */
        $connection = $this->container->get('doctrine')->getConnection();
        $connection->query("TRUNCATE TABLE $table");
    }
}
