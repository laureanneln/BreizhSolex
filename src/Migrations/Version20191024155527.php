<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191024155527 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE address address VARCHAR(255) NOT NULL, CHANGE address2 address2 VARCHAR(255) NOT NULL, CHANGE zip_code zip_code VARCHAR(255) NOT NULL, CHANGE city city VARCHAR(255) NOT NULL, CHANGE phone_number phone_number VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product DROP slug');
        $this->addSql('ALTER TABLE user CHANGE address address VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE address2 address2 VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE zip_code zip_code VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE city city VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE phone_number phone_number VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
