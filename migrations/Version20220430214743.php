<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220430214743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE search_game (id INT AUTO_INCREMENT NOT NULL, attacker_id INT NOT NULL, victim_id INT NOT NULL, attacker_status INT NOT NULL, victim_status INT NOT NULL, INDEX IDX_783124E565F8CAE3 (attacker_id), INDEX IDX_783124E544972A0E (victim_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE search_game ADD CONSTRAINT FK_783124E565F8CAE3 FOREIGN KEY (attacker_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE search_game ADD CONSTRAINT FK_783124E544972A0E FOREIGN KEY (victim_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE player ADD search_status TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE search_game');
        $this->addSql('ALTER TABLE player DROP search_status');
    }
}
