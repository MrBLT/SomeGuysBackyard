<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200519022230 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mssql', 'Migration can only be executed safely on \'mssql\'.');

        $this->addSql('CREATE TABLE member (id UNIQUEIDENTIFIER NOT NULL, email NVARCHAR(180) NOT NULL, first_name NVARCHAR(255) NOT NULL, last_name NVARCHAR(255) NOT NULL, phone NVARCHAR(31), address NVARCHAR(255), address2 NVARCHAR(255), city NVARCHAR(255), state NVARCHAR(31), zipcode NVARCHAR(10), country NVARCHAR(100), roles VARCHAR(MAX), password NVARCHAR(255), is_donor BIT, upload_date DATETIME2(6), join_date DATE, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6) NOT NULL, created_by NVARCHAR(255), updated_by NVARCHAR(255), deleted_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_70E4FA78E7927C74 ON member (email) WHERE email IS NOT NULL');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:uuid)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'member\', N\'COLUMN\', id');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:json)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'member\', N\'COLUMN\', roles');
        $this->addSql('CREATE TABLE member_invitation (id UNIQUEIDENTIFIER NOT NULL, roles VARCHAR(MAX) NOT NULL, create_date DATE NOT NULL, email NVARCHAR(180) NOT NULL, expiration_date DATE NOT NULL, user_created BIT NOT NULL, first_name NVARCHAR(255) NOT NULL, last_name NVARCHAR(255) NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6) NOT NULL, created_by NVARCHAR(255), updated_by NVARCHAR(255), deleted_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:uuid)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'member_invitation\', N\'COLUMN\', id');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:json)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'member_invitation\', N\'COLUMN\', roles');
        $this->addSql('CREATE TABLE member_number (id INT IDENTITY NOT NULL, member_id UNIQUEIDENTIFIER NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B2469D677597D3FE ON member_number (member_id) WHERE member_id IS NOT NULL');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:uuid)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'member_number\', N\'COLUMN\', member_id');
        $this->addSql('CREATE TABLE password_reset_request (id UNIQUEIDENTIFIER NOT NULL, member_id UNIQUEIDENTIFIER NOT NULL, expiration_date DATE NOT NULL, create_date DATE NOT NULL, fulfilled BIT NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6) NOT NULL, created_by NVARCHAR(255), updated_by NVARCHAR(255), deleted_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_C5D0A95A7597D3FE ON password_reset_request (member_id)');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:uuid)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'password_reset_request\', N\'COLUMN\', id');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:uuid)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'password_reset_request\', N\'COLUMN\', member_id');
        $this->addSql('ALTER TABLE member_number ADD CONSTRAINT FK_B2469D677597D3FE FOREIGN KEY (member_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE password_reset_request ADD CONSTRAINT FK_C5D0A95A7597D3FE FOREIGN KEY (member_id) REFERENCES member (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mssql', 'Migration can only be executed safely on \'mssql\'.');

        $this->addSql('CREATE SCHEMA db_accessadmin');
        $this->addSql('CREATE SCHEMA db_backupoperator');
        $this->addSql('CREATE SCHEMA db_datareader');
        $this->addSql('CREATE SCHEMA db_datawriter');
        $this->addSql('CREATE SCHEMA db_ddladmin');
        $this->addSql('CREATE SCHEMA db_denydatareader');
        $this->addSql('CREATE SCHEMA db_denydatawriter');
        $this->addSql('CREATE SCHEMA db_owner');
        $this->addSql('CREATE SCHEMA db_securityadmin');
        $this->addSql('CREATE SCHEMA dbo');
        $this->addSql('ALTER TABLE member_number DROP CONSTRAINT FK_B2469D677597D3FE');
        $this->addSql('ALTER TABLE password_reset_request DROP CONSTRAINT FK_C5D0A95A7597D3FE');
        $this->addSql('DROP TABLE member');
        $this->addSql('DROP TABLE member_invitation');
        $this->addSql('DROP TABLE member_number');
        $this->addSql('DROP TABLE password_reset_request');
    }
}
