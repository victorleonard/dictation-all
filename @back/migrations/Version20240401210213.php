<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240401210213 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        
        if ($platform === 'postgresql') {
            $this->addSql('CREATE TABLE verb_user (verb_id INT NOT NULL, user_id BYTEA NOT NULL, PRIMARY KEY(verb_id, user_id))');
            $this->addSql('CREATE INDEX IDX_D5E43E32C1D03483 ON verb_user (verb_id)');
            $this->addSql('CREATE INDEX IDX_D5E43E32A76ED395 ON verb_user (user_id)');
            $this->addSql('ALTER TABLE verb_user ADD CONSTRAINT FK_D5E43E32C1D03483 FOREIGN KEY (verb_id) REFERENCES verb (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE verb_user ADD CONSTRAINT FK_D5E43E32A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        } else {
            $this->addSql('CREATE TABLE verb_user (verb_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_D5E43E32C1D03483 (verb_id), INDEX IDX_D5E43E32A76ED395 (user_id), PRIMARY KEY(verb_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('ALTER TABLE verb_user ADD CONSTRAINT FK_D5E43E32C1D03483 FOREIGN KEY (verb_id) REFERENCES verb (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE verb_user ADD CONSTRAINT FK_D5E43E32A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        }
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        
        if ($platform === 'postgresql') {
            $this->addSql('ALTER TABLE verb_user DROP CONSTRAINT FK_D5E43E32C1D03483');
            $this->addSql('ALTER TABLE verb_user DROP CONSTRAINT FK_D5E43E32A76ED395');
        } else {
            $this->addSql('ALTER TABLE verb_user DROP FOREIGN KEY FK_D5E43E32C1D03483');
            $this->addSql('ALTER TABLE verb_user DROP FOREIGN KEY FK_D5E43E32A76ED395');
        }
        $this->addSql('DROP TABLE verb_user');
    }
}
