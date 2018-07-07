<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180628063336 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE wall_painting ADD cover_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE wall_painting ADD CONSTRAINT FK_44614656922726E9 FOREIGN KEY (cover_id) REFERENCES media (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_44614656922726E9 ON wall_painting (cover_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE wall_painting DROP FOREIGN KEY FK_44614656922726E9');
        $this->addSql('DROP INDEX UNIQ_44614656922726E9 ON wall_painting');
        $this->addSql('ALTER TABLE wall_painting DROP cover_id');
    }
}
