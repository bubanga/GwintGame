<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220501130716 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE arena DROP attacker_swords, DROP attacker_arches, DROP attacker_catapult, DROP victim_swords, DROP victim_arches, DROP victim_catapult');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE arena ADD attacker_swords LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD attacker_arches LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD attacker_catapult LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD victim_swords LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD victim_arches LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD victim_catapult LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
