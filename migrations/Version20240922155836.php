<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240922155836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Tache table with necessary columns and relationships.';
    }

    public function up(Schema $schema): void
    {
        // This up() migration creates the Tache table
        $this->addSql('CREATE TABLE Tache (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            titre VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            terminee BOOLEAN NOT NULL,
            PRIMARY KEY(id),
            FOREIGN KEY (user_id) REFERENCES User(id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // This down() migration drops the Tache table
        $this->addSql('DROP TABLE Tache');
    }
}
