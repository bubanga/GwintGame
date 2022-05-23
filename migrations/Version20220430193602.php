<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220430193602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE deck_card (deck_id INT NOT NULL, card_id INT NOT NULL, INDEX IDX_2AF3DCED111948DC (deck_id), INDEX IDX_2AF3DCED4ACC9A20 (card_id), PRIMARY KEY(deck_id, card_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE deck_card ADD CONSTRAINT FK_2AF3DCED111948DC FOREIGN KEY (deck_id) REFERENCES deck (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE deck_card ADD CONSTRAINT FK_2AF3DCED4ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE deck DROP deck');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE deck_card');
        $this->addSql('ALTER TABLE deck ADD deck LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
