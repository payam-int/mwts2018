<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180225220911 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_7DAAE07DA76ED395');
        $this->addSql('DROP INDEX UNIQ_7DAAE07D7294869C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__summary_article AS SELECT id, user_id, article_id, title, file, paid, reviewed, accepted FROM summary_article');
        $this->addSql('DROP TABLE summary_article');
        $this->addSql('CREATE TABLE summary_article (id INTEGER NOT NULL, user_id INTEGER DEFAULT NULL, article_id INTEGER DEFAULT NULL, payment_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL COLLATE BINARY, file VARCHAR(255) NOT NULL COLLATE BINARY, paid BOOLEAN Not NULL DEFAULT FALSE, reviewed BOOLEAN Not NULL DEFAULT FALSE, accepted BOOLEAN Not NULL DEFAULT FALSE, PRIMARY KEY(id), CONSTRAINT FK_7DAAE07DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_7DAAE07D7294869C FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_7DAAE07D4C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO summary_article (id, user_id, article_id, title, file, paid, reviewed, accepted) SELECT id, user_id, article_id, title, file, paid, reviewed, accepted FROM __temp__summary_article');
        $this->addSql('DROP TABLE __temp__summary_article');
        $this->addSql('CREATE INDEX IDX_7DAAE07DA76ED395 ON summary_article (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7DAAE07D7294869C ON summary_article (article_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7DAAE07D4C3A3BB ON summary_article (payment_id)');
        $this->addSql('DROP INDEX IDX_6D28840DA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__payment AS SELECT id, user_id, reference_id, price, state, metadata, online_payment_price FROM payment');
        $this->addSql('DROP TABLE payment');
        $this->addSql('CREATE TABLE payment (id INTEGER NOT NULL, user_id INTEGER DEFAULT NULL, reference_id VARCHAR(255) DEFAULT NULL COLLATE BINARY, price VARCHAR(255) NOT NULL COLLATE BINARY, state INTEGER NOT NULL, metadata CLOB NOT NULL COLLATE BINARY --(DC2Type:json)
        , online_payment_price VARCHAR(255) NOT NULL COLLATE BINARY, done BOOLEAN Not NULL DEFAULT FALSE, PRIMARY KEY(id), CONSTRAINT FK_6D28840DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO payment (id, user_id, reference_id, price, state, metadata, online_payment_price) SELECT id, user_id, reference_id, price, state, metadata, online_payment_price FROM __temp__payment');
        $this->addSql('DROP TABLE __temp__payment');
        $this->addSql('CREATE INDEX IDX_6D28840DA76ED395 ON payment (user_id)');
        $this->addSql('DROP INDEX UNIQ_23A0E662AC2D45C');
        $this->addSql('DROP INDEX IDX_23A0E66A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__article AS SELECT id, user_id, summary_id, file, paid FROM article');
        $this->addSql('DROP TABLE article');
        $this->addSql('CREATE TABLE article (id INTEGER NOT NULL, user_id INTEGER DEFAULT NULL, summary_id INTEGER DEFAULT NULL, payment_id INTEGER DEFAULT NULL, file VARCHAR(255) NOT NULL COLLATE BINARY, paid BOOLEAN Not NULL DEFAULT FALSE, PRIMARY KEY(id), CONSTRAINT FK_23A0E66A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_23A0E662AC2D45C FOREIGN KEY (summary_id) REFERENCES summary_article (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_23A0E664C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO article (id, user_id, summary_id, file, paid) SELECT id, user_id, summary_id, file, paid FROM __temp__article');
        $this->addSql('DROP TABLE __temp__article');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_23A0E662AC2D45C ON article (summary_id)');
        $this->addSql('CREATE INDEX IDX_23A0E66A76ED395 ON article (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_23A0E664C3A3BB ON article (payment_id)');
        $this->addSql('DROP INDEX IDX_E1E0B40E4C3A3BB');
        $this->addSql('DROP INDEX IDX_E1E0B40EA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__discount AS SELECT id, user_id, payment_id, price, used FROM discount');
        $this->addSql('DROP TABLE discount');
        $this->addSql('CREATE TABLE discount (id INTEGER NOT NULL, user_id INTEGER DEFAULT NULL, payment_id INTEGER DEFAULT NULL, price VARCHAR(255) NOT NULL COLLATE BINARY, used BOOLEAN Not NULL DEFAULT FALSE, PRIMARY KEY(id), CONSTRAINT FK_E1E0B40EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_E1E0B40E4C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO discount (id, user_id, payment_id, price, used) SELECT id, user_id, payment_id, price, used FROM __temp__discount');
        $this->addSql('DROP TABLE __temp__discount');
        $this->addSql('CREATE INDEX IDX_E1E0B40E4C3A3BB ON discount (payment_id)');
        $this->addSql('CREATE INDEX IDX_E1E0B40EA76ED395 ON discount (user_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, full_name, phone_number, email, password, roles, paid, national_code FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER NOT NULL, full_name VARCHAR(255) NOT NULL COLLATE BINARY, phone_number VARCHAR(255) NOT NULL COLLATE BINARY, email VARCHAR(255) NOT NULL COLLATE BINARY, password VARCHAR(255) NOT NULL COLLATE BINARY, roles CLOB NOT NULL COLLATE BINARY --(DC2Type:array)
        , paid BOOLEAN DEFAULT \'0\' NOT NULL, national_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO user (id, full_name, phone_number, email, password, roles, paid, national_code) SELECT id, full_name, phone_number, email, password, roles, paid, national_code FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_23A0E66A76ED395');
        $this->addSql('DROP INDEX UNIQ_23A0E662AC2D45C');
        $this->addSql('DROP INDEX UNIQ_23A0E664C3A3BB');
        $this->addSql('CREATE TEMPORARY TABLE __temp__article AS SELECT id, user_id, summary_id, file, paid FROM article');
        $this->addSql('DROP TABLE article');
        $this->addSql('CREATE TABLE article (id INTEGER NOT NULL, user_id INTEGER DEFAULT NULL, summary_id INTEGER DEFAULT NULL, file VARCHAR(255) NOT NULL, paid BOOLEAN Not NULL DEFAULT FALSE, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO article (id, user_id, summary_id, file, paid) SELECT id, user_id, summary_id, file, paid FROM __temp__article');
        $this->addSql('DROP TABLE __temp__article');
        $this->addSql('CREATE INDEX IDX_23A0E66A76ED395 ON article (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_23A0E662AC2D45C ON article (summary_id)');
        $this->addSql('DROP INDEX IDX_E1E0B40EA76ED395');
        $this->addSql('DROP INDEX IDX_E1E0B40E4C3A3BB');
        $this->addSql('CREATE TEMPORARY TABLE __temp__discount AS SELECT id, user_id, payment_id, used, price FROM discount');
        $this->addSql('DROP TABLE discount');
        $this->addSql('CREATE TABLE discount (id INTEGER NOT NULL, user_id INTEGER DEFAULT NULL, payment_id INTEGER DEFAULT NULL, used BOOLEAN Not NULL DEFAULT FALSE, price VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO discount (id, user_id, payment_id, used, price) SELECT id, user_id, payment_id, used, price FROM __temp__discount');
        $this->addSql('DROP TABLE __temp__discount');
        $this->addSql('CREATE INDEX IDX_E1E0B40EA76ED395 ON discount (user_id)');
        $this->addSql('CREATE INDEX IDX_E1E0B40E4C3A3BB ON discount (payment_id)');
        $this->addSql('DROP INDEX IDX_6D28840DA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__payment AS SELECT id, user_id, reference_id, price, online_payment_price, state, metadata FROM payment');
        $this->addSql('DROP TABLE payment');
        $this->addSql('CREATE TABLE payment (id INTEGER NOT NULL, user_id INTEGER DEFAULT NULL, reference_id VARCHAR(255) DEFAULT NULL, price VARCHAR(255) NOT NULL, online_payment_price VARCHAR(255) NOT NULL, state INTEGER NOT NULL, metadata CLOB NOT NULL --(DC2Type:json)
        , PRIMARY KEY(id))');
        $this->addSql('INSERT INTO payment (id, user_id, reference_id, price, online_payment_price, state, metadata) SELECT id, user_id, reference_id, price, online_payment_price, state, metadata FROM __temp__payment');
        $this->addSql('DROP TABLE __temp__payment');
        $this->addSql('CREATE INDEX IDX_6D28840DA76ED395 ON payment (user_id)');
        $this->addSql('DROP INDEX IDX_7DAAE07DA76ED395');
        $this->addSql('DROP INDEX UNIQ_7DAAE07D7294869C');
        $this->addSql('DROP INDEX UNIQ_7DAAE07D4C3A3BB');
        $this->addSql('CREATE TEMPORARY TABLE __temp__summary_article AS SELECT id, user_id, article_id, title, reviewed, accepted, paid, file FROM summary_article');
        $this->addSql('DROP TABLE summary_article');
        $this->addSql('CREATE TABLE summary_article (id INTEGER NOT NULL, user_id INTEGER DEFAULT NULL, article_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, reviewed BOOLEAN Not NULL DEFAULT FALSE, accepted BOOLEAN Not NULL DEFAULT FALSE, paid BOOLEAN Not NULL DEFAULT FALSE, file VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO summary_article (id, user_id, article_id, title, reviewed, accepted, paid, file) SELECT id, user_id, article_id, title, reviewed, accepted, paid, file FROM __temp__summary_article');
        $this->addSql('DROP TABLE __temp__summary_article');
        $this->addSql('CREATE INDEX IDX_7DAAE07DA76ED395 ON summary_article (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7DAAE07D7294869C ON summary_article (article_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, full_name, phone_number, national_code, email, password, roles, paid FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER NOT NULL, full_name VARCHAR(255) NOT NULL, phone_number VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles CLOB NOT NULL --(DC2Type:array)
        , paid BOOLEAN DEFAULT \'0\' NOT NULL, national_code VARCHAR(255) DEFAULT \'0\' NOT NULL COLLATE BINARY, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO user (id, full_name, phone_number, national_code, email, password, roles, paid) SELECT id, full_name, phone_number, national_code, email, password, roles, paid FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }
}
