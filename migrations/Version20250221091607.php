<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250221091607 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575786A81FB');
        $this->addSql('DROP INDEX IDX_1323A575786A81FB ON evaluation');
        $this->addSql('ALTER TABLE evaluation DROP iduser_id, CHANGE restaurant_id restaurant_id INT NOT NULL, CHANGE rating rating INT NOT NULL, CHANGE commentaire commentaire LONGTEXT NOT NULL, CHANGE created_at date_publication DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evaluation ADD iduser_id INT DEFAULT NULL, CHANGE restaurant_id restaurant_id INT DEFAULT NULL, CHANGE commentaire commentaire LONGTEXT DEFAULT NULL, CHANGE rating rating INT DEFAULT NULL, CHANGE date_publication created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575786A81FB FOREIGN KEY (iduser_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_1323A575786A81FB ON evaluation (iduser_id)');
    }
}
