<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220505215018 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game_participant (id INT AUTO_INCREMENT NOT NULL, player_id INT NOT NULL, pass TINYINT(1) NOT NULL, wins INT NOT NULL, change_card INT NOT NULL, power_up INT NOT NULL, fraction INT NOT NULL, INDEX IDX_9CA291399E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_participant ADD CONSTRAINT FK_9CA291399E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C65F8CAE3');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C99E6F5DF');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C44972A0E');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C663565CF');
        $this->addSql('DROP INDEX UNIQ_232B318C663565CF ON game');
        $this->addSql('DROP INDEX IDX_232B318C99E6F5DF ON game');
        $this->addSql('DROP INDEX IDX_232B318C65F8CAE3 ON game');
        $this->addSql('DROP INDEX IDX_232B318C44972A0E ON game');
        $this->addSql('ALTER TABLE game DROP attacker_id, DROP victim_id, DROP arena_id, DROP player_id, DROP attacker_pass, DROP victim_pass, DROP attacker_win, DROP victim_win, DROP attacker_change_card, DROP victim_change_card');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE game_participant');
        $this->addSql('ALTER TABLE game ADD attacker_id INT NOT NULL, ADD victim_id INT NOT NULL, ADD arena_id INT NOT NULL, ADD player_id INT DEFAULT NULL, ADD attacker_pass TINYINT(1) NOT NULL, ADD victim_pass TINYINT(1) NOT NULL, ADD attacker_win INT NOT NULL, ADD victim_win INT NOT NULL, ADD attacker_change_card INT NOT NULL, ADD victim_change_card INT NOT NULL');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C65F8CAE3 FOREIGN KEY (attacker_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C99E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C44972A0E FOREIGN KEY (victim_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C663565CF FOREIGN KEY (arena_id) REFERENCES arena (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_232B318C663565CF ON game (arena_id)');
        $this->addSql('CREATE INDEX IDX_232B318C99E6F5DF ON game (player_id)');
        $this->addSql('CREATE INDEX IDX_232B318C65F8CAE3 ON game (attacker_id)');
        $this->addSql('CREATE INDEX IDX_232B318C44972A0E ON game (victim_id)');
    }
}
