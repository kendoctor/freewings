<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180514175405 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE customer_media (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, media_id INT DEFAULT NULL, token VARCHAR(50) NOT NULL, INDEX IDX_A419D4379395C3F3 (customer_id), INDEX IDX_A419D437EA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customer_media ADD CONSTRAINT FK_A419D4379395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE customer_media ADD CONSTRAINT FK_A419D437EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE customer ADD telephone VARCHAR(50) DEFAULT NULL, ADD fax VARCHAR(50) DEFAULT NULL, ADD qq VARCHAR(20) DEFAULT NULL, ADD email VARCHAR(50) DEFAULT NULL, ADD address VARCHAR(255) DEFAULT NULL, ADD weight INT NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE customer_media');
        $this->addSql('ALTER TABLE customer DROP telephone, DROP fax, DROP qq, DROP email, DROP address, DROP weight');
    }
}
