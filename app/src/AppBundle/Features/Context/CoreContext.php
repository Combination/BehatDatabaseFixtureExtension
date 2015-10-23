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
     * @param string $table
     */
    protected function clearTable($table)
    {
        /** @var Connection $connection */
        $connection = $this->container->get('doctrine')->getConnection();
        $connection->query("TRUNCATE TABLE $table");
    }

    /**
     * @Given /^Truncated tables$/
     * @param TableNode $table
     */
    public function thereAreInDbTruncatedTables(TableNode $table)
    {
        foreach ($table->getTable() as $tableNames) {
            foreach ($tableNames as $tableName) {
                $this->clearTable($tableName);
            }
        }
    }
}
