<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180628070134 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer ADD logo_id INT DEFAULT NULL, ADD cover_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09F98F144A FOREIGN KEY (logo_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09922726E9 FOREIGN KEY (cover_id) REFERENCES media (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81398E09F98F144A ON customer (logo_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81398E09922726E9 ON customer (cover_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09F98F144A');
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09922726E9');
        $this->addSql('DROP INDEX UNIQ_81398E09F98F144A ON customer');
        $this->addSql('DROP INDEX UNIQ_81398E09922726E9 ON customer');
        $this->addSql('ALTER TABLE customer DROP logo_id, DROP cover_id');
    }
}
