<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180622081200 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE branch ADD media_id INT DEFAULT NULL, ADD qq VARCHAR(50) NOT NULL, DROP qq1, DROP qq2, DROP qq3');
        $this->addSql('ALTER TABLE branch ADD CONSTRAINT FK_BB861B1FEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BB861B1FEA9FDD75 ON branch (media_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE branch DROP FOREIGN KEY FK_BB861B1FEA9FDD75');
        $this->addSql('DROP INDEX UNIQ_BB861B1FEA9FDD75 ON branch');
        $this->addSql('ALTER TABLE branch ADD qq1 VARCHAR(50) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD qq2 VARCHAR(50) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD qq3 VARCHAR(50) DEFAULT NULL COLLATE utf8mb4_unicode_ci, DROP media_id, DROP qq');
    }
}
