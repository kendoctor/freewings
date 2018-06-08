<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180511163119 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE wall_painting_photo (id INT AUTO_INCREMENT NOT NULL, wall_painting_id INT DEFAULT NULL, media_id INT DEFAULT NULL, INDEX IDX_67E29BFD92E62E3B (wall_painting_id), INDEX IDX_67E29BFDEA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_group (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_group_user (user_group_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_3AE4BD51ED93D47 (user_group_id), INDEX IDX_3AE4BD5A76ED395 (user_id), PRIMARY KEY(user_group_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE wall_painting_photo ADD CONSTRAINT FK_67E29BFD92E62E3B FOREIGN KEY (wall_painting_id) REFERENCES wall_painting (id)');
        $this->addSql('ALTER TABLE wall_painting_photo ADD CONSTRAINT FK_67E29BFDEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE user_group_user ADD CONSTRAINT FK_3AE4BD51ED93D47 FOREIGN KEY (user_group_id) REFERENCES user_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group_user ADD CONSTRAINT FK_3AE4BD5A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_group_user DROP FOREIGN KEY FK_3AE4BD51ED93D47');
        $this->addSql('DROP TABLE wall_painting_photo');
        $this->addSql('DROP TABLE user_group');
        $this->addSql('DROP TABLE user_group_user');
    }
}
