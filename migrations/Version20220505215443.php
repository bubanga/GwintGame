<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220505215443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE arena');
        $this->addSql('ALTER TABLE game ADD attacker_id INT NOT NULL, ADD victim_id INT NOT NULL, ADD freeze TINYINT(1) NOT NULL, ADD rain TINYINT(1) NOT NULL, ADD fog TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C65F8CAE3 FOREIGN KEY (attacker_id) REFERENCES game_participant (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C44972A0E FOREIGN KEY (victim_id) REFERENCES game_participant (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_232B318C65F8CAE3 ON game (attacker_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_232B318C44972A0E ON game (victim_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE arena (id INT AUTO_INCREMENT NOT NULL, attacker_deck LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', victim_deck LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', attacker_powerup INT NOT NULL, victim_powerup INT NOT NULL, freeze TINYINT(1) NOT NULL, rain TINYINT(1) NOT NULL, fog TINYINT(1) NOT NULL, attacker_fraction INT NOT NULL, victim_fraction INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C65F8CAE3');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C44972A0E');
        $this->addSql('DROP INDEX UNIQ_232B318C65F8CAE3 ON game');
        $this->addSql('DROP INDEX UNIQ_232B318C44972A0E ON game');
        $this->addSql('ALTER TABLE game DROP attacker_id, DROP victim_id, DROP freeze, DROP rain, DROP fog');
    }
}
