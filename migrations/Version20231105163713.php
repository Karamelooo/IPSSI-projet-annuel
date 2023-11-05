<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231105163713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE upload_file (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(100) NOT NULL, size INT NOT NULL, date DATETIME NOT NULL, last_action DATETIME NOT NULL, owner VARCHAR(255) NOT NULL, file VARCHAR(255) NOT NULL, INDEX IDX_81BB169A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE upload_file ADD CONSTRAINT FK_81BB169A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD storage_use DOUBLE PRECISION NOT NULL, CHANGE stockage stockage DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE upload_file DROP FOREIGN KEY FK_81BB169A76ED395');
        $this->addSql('DROP TABLE upload_file');
        $this->addSql('ALTER TABLE user DROP storage_use, CHANGE stockage stockage DOUBLE PRECISION DEFAULT NULL');
    }
}
