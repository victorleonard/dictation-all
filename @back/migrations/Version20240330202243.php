<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240330202243 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        
        if ($platform === 'postgresql') {
            $this->addSql('CREATE TABLE word_error (id SERIAL NOT NULL, word_id BYTEA NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX IDX_836EB7B7E357438D ON word_error (word_id)');
            $this->addSql('ALTER TABLE word_error ADD CONSTRAINT FK_836EB7B7E357438D FOREIGN KEY (word_id) REFERENCES word (id)');
        } else {
            $this->addSql('CREATE TABLE word_error (id INT AUTO_INCREMENT NOT NULL, word_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', value VARCHAR(255) NOT NULL, INDEX IDX_836EB7B7E357438D (word_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('ALTER TABLE word_error ADD CONSTRAINT FK_836EB7B7E357438D FOREIGN KEY (word_id) REFERENCES word (id)');
        }
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        
        if ($platform === 'postgresql') {
            $this->addSql('ALTER TABLE word_error DROP CONSTRAINT FK_836EB7B7E357438D');
        } else {
            $this->addSql('ALTER TABLE word_error DROP FOREIGN KEY FK_836EB7B7E357438D');
        }
        $this->addSql('DROP TABLE word_error');
    }
}
