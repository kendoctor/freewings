<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180509192432 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE post_translation DROP FOREIGN KEY FK_5829CF40232D562B');
        $this->addSql('ALTER TABLE post_translation ADD CONSTRAINT FK_5829CF40232D562B FOREIGN KEY (object_id) REFERENCES post (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE post_translation DROP FOREIGN KEY FK_5829CF40232D562B');
        $this->addSql('ALTER TABLE post_translation ADD CONSTRAINT FK_5829CF40232D562B FOREIGN KEY (object_id) REFERENCES category (id) ON DELETE CASCADE');
    }
}
