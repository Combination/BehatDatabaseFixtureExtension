<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151023161521 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE `f_users` (
          `id` INT,
          `name` VARCHAR(255)
        );');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) {}
}
