<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240401204205 extends AbstractMigration
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
            $this->addSql('ALTER TABLE verb DROP CONSTRAINT IF EXISTS verb_pkey');
            $this->addSql('DROP SEQUENCE IF EXISTS verb_id_seq CASCADE');
            // Supprimer les contraintes de clé étrangère qui référencent verb.id
            $this->addSql('ALTER TABLE verb_user DROP CONSTRAINT IF EXISTS FK_D5E43E32C1D03483');
            // Changer verb_user.verb_id de INT en BYTEA aussi
            $this->addSql('ALTER TABLE verb_user DROP CONSTRAINT IF EXISTS verb_user_pkey');
            $this->addSql('ALTER TABLE verb_user DROP COLUMN verb_id');
            $this->addSql('ALTER TABLE verb_user ADD COLUMN verb_id BYTEA NOT NULL');
            // Supprimer la colonne verb.id et la recréer en BYTEA
            $this->addSql('ALTER TABLE verb DROP COLUMN id');
            $this->addSql('ALTER TABLE verb ADD COLUMN id BYTEA NOT NULL');
            $this->addSql('ALTER TABLE verb ADD PRIMARY KEY (id)');
            // Recréer la contrainte de clé primaire composite et la clé étrangère
            $this->addSql('ALTER TABLE verb_user ADD PRIMARY KEY (verb_id, user_id)');
            $this->addSql('ALTER TABLE verb_user ADD CONSTRAINT FK_D5E43E32C1D03483 FOREIGN KEY (verb_id) REFERENCES verb (id) ON DELETE CASCADE');
        } else {
            $this->addSql('ALTER TABLE verb CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        }
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        
        if ($platform === 'postgresql') {
            // Note: Reverting from BYTEA to SERIAL is complex and may not be fully reversible
            $this->addSql('ALTER TABLE verb DROP CONSTRAINT verb_pkey');
            $this->addSql('ALTER TABLE verb ALTER COLUMN id TYPE INTEGER');
            $this->addSql('CREATE SEQUENCE verb_id_seq OWNED BY verb.id');
            $this->addSql('ALTER TABLE verb ALTER COLUMN id SET DEFAULT nextval(\'verb_id_seq\')');
            $this->addSql('ALTER TABLE verb ADD PRIMARY KEY (id)');
        } else {
            $this->addSql('ALTER TABLE verb CHANGE id id INT AUTO_INCREMENT NOT NULL');
        }
    }
}
