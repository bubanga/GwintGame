<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220421164736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE arena (id INT AUTO_INCREMENT NOT NULL, attacker_deck LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', victim_deck LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', field_swords INT NOT NULL, field_arches INT NOT NULL, field_catapult INT NOT NULL, attacker_swords LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', attacker_arches LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', attacker_catapult LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', victim_swords LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', victim_arches LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', victim_catapult LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE card (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, unit_type INT NOT NULL, power INT NOT NULL, skill INT NOT NULL, special TINYINT(1) NOT NULL, fraction INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE deck (id INT AUTO_INCREMENT NOT NULL, player_id INT NOT NULL, deck LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', UNIQUE INDEX UNIQ_4FAC363799E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, attacker_id INT NOT NULL, victim_id INT NOT NULL, arena_id INT DEFAULT NULL, turn_id INT NOT NULL, round INT NOT NULL, attacker_pass TINYINT(1) NOT NULL, victim_pass TINYINT(1) NOT NULL, INDEX IDX_232B318C65F8CAE3 (attacker_id), INDEX IDX_232B318C44972A0E (victim_id), UNIQUE INDEX UNIQ_232B318C663565CF (arena_id), INDEX IDX_232B318C1F4F9889 (turn_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE deck ADD CONSTRAINT FK_4FAC363799E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C65F8CAE3 FOREIGN KEY (attacker_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C44972A0E FOREIGN KEY (victim_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C663565CF FOREIGN KEY (arena_id) REFERENCES arena (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C1F4F9889 FOREIGN KEY (turn_id) REFERENCES player (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C663565CF');
        $this->addSql('ALTER TABLE deck DROP FOREIGN KEY FK_4FAC363799E6F5DF');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C65F8CAE3');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C44972A0E');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C1F4F9889');
        $this->addSql('DROP TABLE arena');
        $this->addSql('DROP TABLE card');
        $this->addSql('DROP TABLE deck');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE player');
    }
}
