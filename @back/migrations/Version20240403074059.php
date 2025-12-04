<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240403074059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        
        if ($platform === 'postgresql') {
            $this->addSql('CREATE TABLE log (id SERIAL NOT NULL, user_id BYTEA DEFAULT NULL, type VARCHAR(255) NOT NULL, question JSON DEFAULT NULL, answer JSON DEFAULT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX IDX_8F3F68C5A76ED395 ON log (user_id)');
            $this->addSql('ALTER TABLE log ADD CONSTRAINT FK_8F3F68C5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        } else {
            $this->addSql('CREATE TABLE log (id INT AUTO_INCREMENT NOT NULL, user_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', type VARCHAR(255) NOT NULL, question JSON DEFAULT NULL, answer JSON DEFAULT NULL, INDEX IDX_8F3F68C5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('ALTER TABLE log ADD CONSTRAINT FK_8F3F68C5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        }
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        
        if ($platform === 'postgresql') {
            $this->addSql('ALTER TABLE log DROP CONSTRAINT FK_8F3F68C5A76ED395');
        } else {
            $this->addSql('ALTER TABLE log DROP FOREIGN KEY FK_8F3F68C5A76ED395');
        }
        $this->addSql('DROP TABLE log');
    }
}
