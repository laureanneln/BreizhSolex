<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191216151629 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_BA388B7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE customer_order CHANGE order_date order_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE address address VARCHAR(255) NOT NULL, CHANGE address2 address2 VARCHAR(255) NOT NULL, CHANGE zip_code zip_code VARCHAR(255) NOT NULL, CHANGE city city VARCHAR(255) NOT NULL, CHANGE phone_number phone_number VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE preference CHANGE taxe taxe INT NOT NULL');
        $this->addSql('ALTER TABLE product CHANGE no_taxe_price no_taxe_price DOUBLE PRECISION NOT NULL, CHANGE quantity quantity INT NOT NULL, CHANGE added_date added_date DATETIME NOT NULL, CHANGE slug slug VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE category CHANGE position position INT NOT NULL');
        $this->addSql('ALTER TABLE subcategory CHANGE slug slug VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE item ADD cart_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E1AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('CREATE INDEX IDX_1F1B251E1AD5CDBF ON item (cart_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E1AD5CDBF');
        $this->addSql('DROP TABLE cart');
        $this->addSql('ALTER TABLE category CHANGE position position INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_order CHANGE order_date order_date DATETIME DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_1F1B251E1AD5CDBF ON item');
        $this->addSql('ALTER TABLE item DROP cart_id');
        $this->addSql('ALTER TABLE preference CHANGE taxe taxe INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product CHANGE no_taxe_price no_taxe_price DOUBLE PRECISION DEFAULT NULL, CHANGE quantity quantity INT DEFAULT NULL, CHANGE added_date added_date DATETIME DEFAULT NULL, CHANGE slug slug VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE subcategory CHANGE slug slug VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user CHANGE address address VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE address2 address2 VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE zip_code zip_code VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE city city VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE phone_number phone_number VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
