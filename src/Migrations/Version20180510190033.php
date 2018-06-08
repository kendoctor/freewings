<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180510190033 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE wall_painting_artist (id INT AUTO_INCREMENT NOT NULL, wall_painting_id INT DEFAULT NULL, artist_id INT DEFAULT NULL, INDEX IDX_D14AD56F92E62E3B (wall_painting_id), INDEX IDX_D14AD56FB7970CF8 (artist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE wall_painting_artist ADD CONSTRAINT FK_D14AD56F92E62E3B FOREIGN KEY (wall_painting_id) REFERENCES wall_painting (id)');
        $this->addSql('ALTER TABLE wall_painting_artist ADD CONSTRAINT FK_D14AD56FB7970CF8 FOREIGN KEY (artist_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE wall_painting_artist');
    }
}
