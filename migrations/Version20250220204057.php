<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250220204057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evaluation ADD restaurant_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A57535592D86 FOREIGN KEY (restaurant_id_id) REFERENCES restaurants (id)');
        $this->addSql('CREATE INDEX IDX_1323A57535592D86 ON evaluation (restaurant_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A57535592D86');
        $this->addSql('DROP INDEX IDX_1323A57535592D86 ON evaluation');
        $this->addSql('ALTER TABLE evaluation DROP restaurant_id_id');
    }
}
