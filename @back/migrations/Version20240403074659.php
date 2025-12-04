<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240403074659 extends AbstractMigration
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
            $this->addSql('ALTER TABLE log DROP CONSTRAINT IF EXISTS log_pkey');
            $this->addSql('DROP SEQUENCE IF EXISTS log_id_seq CASCADE');
            // Supprimer la colonne et la recréer en BYTEA
            $this->addSql('ALTER TABLE log DROP COLUMN id');
            $this->addSql('ALTER TABLE log ADD COLUMN id BYTEA NOT NULL');
            $this->addSql('ALTER TABLE log ADD PRIMARY KEY (id)');
        } else {
            $this->addSql('ALTER TABLE log CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        }
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        
        if ($platform === 'postgresql') {
            // Note: Reverting from BYTEA to SERIAL is complex and may not be fully reversible
            // This is a simplified version
            $this->addSql('ALTER TABLE log DROP CONSTRAINT log_pkey');
            $this->addSql('ALTER TABLE log ALTER COLUMN id TYPE INTEGER');
            $this->addSql('CREATE SEQUENCE log_id_seq OWNED BY log.id');
            $this->addSql('ALTER TABLE log ALTER COLUMN id SET DEFAULT nextval(\'log_id_seq\')');
            $this->addSql('ALTER TABLE log ADD PRIMARY KEY (id)');
        } else {
            $this->addSql('ALTER TABLE log CHANGE id id INT AUTO_INCREMENT NOT NULL');
        }
    }
}
