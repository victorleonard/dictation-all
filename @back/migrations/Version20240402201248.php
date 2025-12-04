<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240402201248 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        
        if ($platform === 'postgresql') {
            $this->addSql('CREATE TABLE time (id BYTEA NOT NULL, hour VARCHAR(255) NOT NULL, minute VARCHAR(255) NOT NULL, meridiem VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE TABLE time_user (time_id BYTEA NOT NULL, user_id BYTEA NOT NULL, PRIMARY KEY(time_id, user_id))');
            $this->addSql('CREATE INDEX IDX_202EDF645EEADD3B ON time_user (time_id)');
            $this->addSql('CREATE INDEX IDX_202EDF64A76ED395 ON time_user (user_id)');
            $this->addSql('ALTER TABLE time_user ADD CONSTRAINT FK_202EDF645EEADD3B FOREIGN KEY (time_id) REFERENCES time (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE time_user ADD CONSTRAINT FK_202EDF64A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        } else {
            $this->addSql('CREATE TABLE time (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', hour VARCHAR(255) NOT NULL, minute VARCHAR(255) NOT NULL, meridiem VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE TABLE time_user (time_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_202EDF645EEADD3B (time_id), INDEX IDX_202EDF64A76ED395 (user_id), PRIMARY KEY(time_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('ALTER TABLE time_user ADD CONSTRAINT FK_202EDF645EEADD3B FOREIGN KEY (time_id) REFERENCES time (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE time_user ADD CONSTRAINT FK_202EDF64A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        }
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        
        if ($platform === 'postgresql') {
            $this->addSql('ALTER TABLE time_user DROP CONSTRAINT FK_202EDF645EEADD3B');
            $this->addSql('ALTER TABLE time_user DROP CONSTRAINT FK_202EDF64A76ED395');
        } else {
            $this->addSql('ALTER TABLE time_user DROP FOREIGN KEY FK_202EDF645EEADD3B');
            $this->addSql('ALTER TABLE time_user DROP FOREIGN KEY FK_202EDF64A76ED395');
        }
        $this->addSql('DROP TABLE time');
        $this->addSql('DROP TABLE time_user');
    }
}
