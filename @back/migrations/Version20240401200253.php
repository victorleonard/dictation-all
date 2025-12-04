<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240401200253 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        
        if ($platform === 'postgresql') {
            $this->addSql('CREATE TABLE verb (id SERIAL NOT NULL, value VARCHAR(255) NOT NULL, type VARCHAR(255) DEFAULT NULL, time VARCHAR(255) NOT NULL, s1 VARCHAR(255) NOT NULL, s2 VARCHAR(255) NOT NULL, s3 VARCHAR(255) NOT NULL, p1 VARCHAR(255) NOT NULL, p2 VARCHAR(255) NOT NULL, p3 VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        } else {
            $this->addSql('CREATE TABLE verb (id INT AUTO_INCREMENT NOT NULL, value VARCHAR(255) NOT NULL, type VARCHAR(255) DEFAULT NULL, time VARCHAR(255) NOT NULL, s1 VARCHAR(255) NOT NULL, s2 VARCHAR(255) NOT NULL, s3 VARCHAR(255) NOT NULL, p1 VARCHAR(255) NOT NULL, p2 VARCHAR(255) NOT NULL, p3 VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE verb');
    }
}
