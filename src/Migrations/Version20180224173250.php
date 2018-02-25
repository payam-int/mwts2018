<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180224173250 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_7DAAE07DA76ED395');
        $this->addSql('DROP INDEX UNIQ_7DAAE07D7294869C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__summary_article AS SELECT id, user_id, article_id, title, state, file FROM summary_article');
        $this->addSql('DROP TABLE summary_article');
        $this->addSql('CREATE TABLE summary_article (id INTEGER NOT NULL, user_id INTEGER DEFAULT NULL, article_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL COLLATE BINARY, state VARCHAR(255) DEFAULT NULL COLLATE BINARY, file VARCHAR(255) NOT NULL COLLATE BINARY, paid BOOLEAN NOT NULL DEFAULT FALSE , PRIMARY KEY(id), CONSTRAINT FK_7DAAE07DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_7DAAE07D7294869C FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO summary_article (id, user_id, article_id, title, state, file) SELECT id, user_id, article_id, title, state, file FROM __temp__summary_article');
        $this->addSql('DROP TABLE __temp__summary_article');
        $this->addSql('CREATE INDEX IDX_7DAAE07DA76ED395 ON summary_article (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7DAAE07D7294869C ON summary_article (article_id)');
        $this->addSql('DROP INDEX UNIQ_23A0E662AC2D45C');
        $this->addSql('DROP INDEX IDX_23A0E66A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__article AS SELECT id, user_id, summary_id, state, file FROM article');
        $this->addSql('DROP TABLE article');
        $this->addSql('CREATE TABLE article (id INTEGER NOT NULL, user_id INTEGER DEFAULT NULL, summary_id INTEGER DEFAULT NULL, state VARCHAR(255) NOT NULL COLLATE BINARY, file VARCHAR(255) NOT NULL COLLATE BINARY, PRIMARY KEY(id), CONSTRAINT FK_23A0E66A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_23A0E662AC2D45C FOREIGN KEY (summary_id) REFERENCES summary_article (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO article (id, user_id, summary_id, state, file) SELECT id, user_id, summary_id, state, file FROM __temp__article');
        $this->addSql('DROP TABLE __temp__article');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_23A0E662AC2D45C ON article (summary_id)');
        $this->addSql('CREATE INDEX IDX_23A0E66A76ED395 ON article (user_id)');
        $this->addSql('DROP INDEX IDX_E1E0B40EA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__discount AS SELECT id, user_id, used, price FROM discount');
        $this->addSql('DROP TABLE discount');
        $this->addSql('CREATE TABLE discount (id INTEGER NOT NULL, user_id INTEGER DEFAULT NULL, used BOOLEAN NOT NULL, price VARCHAR(255) NOT NULL COLLATE BINARY, PRIMARY KEY(id), CONSTRAINT FK_E1E0B40EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO discount (id, user_id, used, price) SELECT id, user_id, used, price FROM __temp__discount');
        $this->addSql('DROP TABLE __temp__discount');
        $this->addSql('CREATE INDEX IDX_E1E0B40EA76ED395 ON discount (user_id)');
        $this->addSql('DROP INDEX IDX_6D28840DA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__payment AS SELECT id, user_id, reference_id, price, state, metadata FROM payment');
        $this->addSql('DROP TABLE payment');
        $this->addSql('CREATE TABLE payment (id INTEGER NOT NULL, user_id INTEGER DEFAULT NULL, reference_id VARCHAR(255) DEFAULT NULL COLLATE BINARY, price VARCHAR(255) NOT NULL COLLATE BINARY, state INTEGER NOT NULL, metadata CLOB NOT NULL COLLATE BINARY --(DC2Type:json)
        , PRIMARY KEY(id), CONSTRAINT FK_6D28840DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO payment (id, user_id, reference_id, price, state, metadata) SELECT id, user_id, reference_id, price, state, metadata FROM __temp__payment');
        $this->addSql('DROP TABLE __temp__payment');
        $this->addSql('CREATE INDEX IDX_6D28840DA76ED395 ON payment (user_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_23A0E66A76ED395');
        $this->addSql('DROP INDEX UNIQ_23A0E662AC2D45C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__article AS SELECT id, user_id, summary_id, state, file FROM article');
        $this->addSql('DROP TABLE article');
        $this->addSql('CREATE TABLE article (id INTEGER NOT NULL, user_id INTEGER DEFAULT NULL, summary_id INTEGER DEFAULT NULL, state VARCHAR(255) NOT NULL, file VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO article (id, user_id, summary_id, state, file) SELECT id, user_id, summary_id, state, file FROM __temp__article');
        $this->addSql('DROP TABLE __temp__article');
        $this->addSql('CREATE INDEX IDX_23A0E66A76ED395 ON article (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_23A0E662AC2D45C ON article (summary_id)');
        $this->addSql('DROP INDEX IDX_E1E0B40EA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__discount AS SELECT id, user_id, used, price FROM discount');
        $this->addSql('DROP TABLE discount');
        $this->addSql('CREATE TABLE discount (id INTEGER NOT NULL, user_id INTEGER DEFAULT NULL, used BOOLEAN NOT NULL, price VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO discount (id, user_id, used, price) SELECT id, user_id, used, price FROM __temp__discount');
        $this->addSql('DROP TABLE __temp__discount');
        $this->addSql('CREATE INDEX IDX_E1E0B40EA76ED395 ON discount (user_id)');
        $this->addSql('DROP INDEX IDX_6D28840DA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__payment AS SELECT id, user_id, reference_id, price, state, metadata FROM payment');
        $this->addSql('DROP TABLE payment');
        $this->addSql('CREATE TABLE payment (id INTEGER NOT NULL, user_id INTEGER DEFAULT NULL, reference_id VARCHAR(255) DEFAULT NULL, price VARCHAR(255) NOT NULL, state INTEGER NOT NULL, metadata CLOB NOT NULL --(DC2Type:json)
        , PRIMARY KEY(id))');
        $this->addSql('INSERT INTO payment (id, user_id, reference_id, price, state, metadata) SELECT id, user_id, reference_id, price, state, metadata FROM __temp__payment');
        $this->addSql('DROP TABLE __temp__payment');
        $this->addSql('CREATE INDEX IDX_6D28840DA76ED395 ON payment (user_id)');
        $this->addSql('DROP INDEX IDX_7DAAE07DA76ED395');
        $this->addSql('DROP INDEX UNIQ_7DAAE07D7294869C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__summary_article AS SELECT id, user_id, article_id, title, state, file FROM summary_article');
        $this->addSql('DROP TABLE summary_article');
        $this->addSql('CREATE TABLE summary_article (id INTEGER NOT NULL, user_id INTEGER DEFAULT NULL, article_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, state VARCHAR(255) DEFAULT NULL, file VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO summary_article (id, user_id, article_id, title, state, file, paid) SELECT id, user_id, article_id, title, state, file, FALSE FROM __temp__summary_article');
        $this->addSql('DROP TABLE __temp__summary_article');
        $this->addSql('CREATE INDEX IDX_7DAAE07DA76ED395 ON summary_article (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7DAAE07D7294869C ON summary_article (article_id)');
    }
}