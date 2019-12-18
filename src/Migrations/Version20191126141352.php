<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191126141352 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product CHANGE no_taxe_price no_taxe_price DOUBLE PRECISION DEFAULT NULL, CHANGE quantity quantity INT DEFAULT NULL, CHANGE added_date added_date DATETIME DEFAULT NULL, CHANGE slug slug VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE preference ADD taxe INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_order CHANGE order_date order_date DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_order CHANGE order_date order_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE preference DROP taxe');
        $this->addSql('ALTER TABLE product CHANGE no_taxe_price no_taxe_price DOUBLE PRECISION DEFAULT NULL, CHANGE quantity quantity INT DEFAULT 1 NOT NULL, CHANGE added_date added_date DATETIME DEFAULT NULL, CHANGE slug slug VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
