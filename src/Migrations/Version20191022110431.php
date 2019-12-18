<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191022110431 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP admin');
        $this->addSql('ALTER TABLE role_user MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE role_user DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE role_user ADD role_id INT NOT NULL, ADD user_id INT NOT NULL, DROP id');
        $this->addSql('ALTER TABLE role_user ADD CONSTRAINT FK_332CA4DDD60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_user ADD CONSTRAINT FK_332CA4DDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_332CA4DDD60322AC ON role_user (role_id)');
        $this->addSql('CREATE INDEX IDX_332CA4DDA76ED395 ON role_user (user_id)');
        $this->addSql('ALTER TABLE role_user ADD PRIMARY KEY (role_id, user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE role_user DROP FOREIGN KEY FK_332CA4DDD60322AC');
        $this->addSql('ALTER TABLE role_user DROP FOREIGN KEY FK_332CA4DDA76ED395');
        $this->addSql('DROP INDEX IDX_332CA4DDD60322AC ON role_user');
        $this->addSql('DROP INDEX IDX_332CA4DDA76ED395 ON role_user');
        $this->addSql('ALTER TABLE role_user DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE role_user ADD id INT AUTO_INCREMENT NOT NULL, DROP role_id, DROP user_id');
        $this->addSql('ALTER TABLE role_user ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE user ADD admin TINYINT(1) NOT NULL');
    }
}
