<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220430195749 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE deck_card (id INT AUTO_INCREMENT NOT NULL, card_id INT DEFAULT NULL, deck_id INT DEFAULT NULL, INDEX IDX_2AF3DCED4ACC9A20 (card_id), INDEX IDX_2AF3DCED111948DC (deck_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE deck_card ADD CONSTRAINT FK_2AF3DCED4ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id)');
        $this->addSql('ALTER TABLE deck_card ADD CONSTRAINT FK_2AF3DCED111948DC FOREIGN KEY (deck_id) REFERENCES deck (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE deck_card');
    }
}
