<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240330212021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        
        if ($platform === 'postgresql') {
            // PostgreSQL: changer SERIAL en BYTEA nécessite plusieurs étapes
            // Note: Cette conversion suppose que la table peut être vide ou que les IDs peuvent être régénérés
            $this->addSql('ALTER TABLE word_error DROP CONSTRAINT IF EXISTS word_error_pkey');
            $this->addSql('DROP SEQUENCE IF EXISTS word_error_id_seq CASCADE');
            // Supprimer la colonne et la recréer en BYTEA
            $this->addSql('ALTER TABLE word_error DROP COLUMN id');
            $this->addSql('ALTER TABLE word_error ADD COLUMN id BYTEA NOT NULL');
            $this->addSql('ALTER TABLE word_error ADD PRIMARY KEY (id)');
        } else {
            $this->addSql('ALTER TABLE word_error CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        }
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        
        if ($platform === 'postgresql') {
            // Note: Reverting from BYTEA to SERIAL is complex and may not be fully reversible
            $this->addSql('ALTER TABLE word_error DROP CONSTRAINT word_error_pkey');
            $this->addSql('ALTER TABLE word_error ALTER COLUMN id TYPE INTEGER');
            $this->addSql('CREATE SEQUENCE word_error_id_seq OWNED BY word_error.id');
            $this->addSql('ALTER TABLE word_error ALTER COLUMN id SET DEFAULT nextval(\'word_error_id_seq\')');
            $this->addSql('ALTER TABLE word_error ADD PRIMARY KEY (id)');
        } else {
            $this->addSql('ALTER TABLE word_error CHANGE id id INT AUTO_INCREMENT NOT NULL');
        }
    }
}
