<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250708162553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notification_attempts (id VARCHAR(36) NOT NULL, notification_id VARCHAR(36) NOT NULL, provider_name VARCHAR(100) NOT NULL, attempted_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, successful BOOLEAN NOT NULL, error_message TEXT DEFAULT NULL, provider_response TEXT DEFAULT NULL, http_status_code INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EAD7DFC1EF1A9D84 ON notification_attempts (notification_id)');
        $this->addSql('COMMENT ON COLUMN notification_attempts.attempted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE notifications (id VARCHAR(36) NOT NULL, user_id VARCHAR(255) NOT NULL, channel VARCHAR(16) NOT NULL, status VARCHAR(16) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, sent_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, max_retries INT NOT NULL, current_retries INT NOT NULL, scheduled_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, metadata JSON NOT NULL, recipient_email VARCHAR(255) DEFAULT NULL, recipient_phone_number VARCHAR(32) DEFAULT NULL, recipient_user_id VARCHAR(64) DEFAULT NULL, recipient_push_token VARCHAR(255) DEFAULT NULL, recipient_name VARCHAR(255) DEFAULT NULL, message_subject VARCHAR(255) NOT NULL, message_body VARCHAR(255) NOT NULL, message_template_variables JSON NOT NULL, message_template_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN notifications.channel IS \'(DC2Type:notification_channel)\'');
        $this->addSql('COMMENT ON COLUMN notifications.status IS \'(DC2Type:notification_status)\'');
        $this->addSql('COMMENT ON COLUMN notifications.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN notifications.sent_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN notifications.scheduled_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN notifications.recipient_email IS \'(DC2Type:email)\'');
        $this->addSql('COMMENT ON COLUMN notifications.recipient_phone_number IS \'(DC2Type:phone_number)\'');
        $this->addSql('COMMENT ON COLUMN notifications.recipient_user_id IS \'(DC2Type:user_id)\'');
        $this->addSql('ALTER TABLE notification_attempts ADD CONSTRAINT FK_EAD7DFC1EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notifications (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE notification_attempts DROP CONSTRAINT FK_EAD7DFC1EF1A9D84');
        $this->addSql('DROP TABLE notification_attempts');
        $this->addSql('DROP TABLE notifications');
    }
}
