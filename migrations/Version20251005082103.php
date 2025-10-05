<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251005082103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bookings (id UUID NOT NULL, truck_id UUID NOT NULL, renter_id UUID NOT NULL, booking_number VARCHAR(50) NOT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, total_amount NUMERIC(10, 2) NOT NULL, deposit_amount NUMERIC(10, 2) DEFAULT NULL, status VARCHAR(20) NOT NULL, payment_status VARCHAR(20) NOT NULL, notes TEXT DEFAULT NULL, pickup_location TEXT DEFAULT NULL, dropoff_location TEXT DEFAULT NULL, purpose TEXT DEFAULT NULL, confirmed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, cancelled_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, cancellation_reason TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7A853C352D0A24AA ON bookings (booking_number)');
        $this->addSql('CREATE INDEX IDX_7A853C35C6957CCE ON bookings (truck_id)');
        $this->addSql('CREATE INDEX IDX_7A853C35E289A545 ON bookings (renter_id)');
        $this->addSql('COMMENT ON COLUMN bookings.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN bookings.truck_id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN bookings.renter_id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN bookings.start_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN bookings.end_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN bookings.confirmed_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN bookings.cancelled_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN bookings.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN bookings.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE companies (id UUID NOT NULL, name VARCHAR(100) NOT NULL, dot_number VARCHAR(20) DEFAULT NULL, mc_number VARCHAR(20) DEFAULT NULL, tax_id VARCHAR(50) DEFAULT NULL, email VARCHAR(100) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, website VARCHAR(100) DEFAULT NULL, logo TEXT DEFAULT NULL, status VARCHAR(20) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, address VARCHAR(200) DEFAULT NULL, city VARCHAR(100) DEFAULT NULL, state VARCHAR(50) DEFAULT NULL, zip_code VARCHAR(20) DEFAULT NULL, country VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8244AA3A62893ACA ON companies (dot_number)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8244AA3AE78EF2AC ON companies (mc_number)');
        $this->addSql('COMMENT ON COLUMN companies.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN companies.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN companies.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE truck_documents (id UUID NOT NULL, truck_id UUID NOT NULL, document_type VARCHAR(50) NOT NULL, file_name VARCHAR(255) NOT NULL, url TEXT NOT NULL, mime_type VARCHAR(50) DEFAULT NULL, file_size INT DEFAULT NULL, expiry_date DATE DEFAULT NULL, document_number VARCHAR(100) DEFAULT NULL, notes TEXT DEFAULT NULL, uploaded_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D7B61030C6957CCE ON truck_documents (truck_id)');
        $this->addSql('COMMENT ON COLUMN truck_documents.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN truck_documents.truck_id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN truck_documents.uploaded_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE truck_images (id UUID NOT NULL, truck_id UUID NOT NULL, file_name VARCHAR(255) NOT NULL, url TEXT NOT NULL, mime_type VARCHAR(50) DEFAULT NULL, file_size INT DEFAULT NULL, display_order INT NOT NULL, is_primary BOOLEAN NOT NULL, caption VARCHAR(255) DEFAULT NULL, uploaded_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_86985E11C6957CCE ON truck_images (truck_id)');
        $this->addSql('COMMENT ON COLUMN truck_images.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN truck_images.truck_id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN truck_images.uploaded_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE trucks (id UUID NOT NULL, company_id UUID DEFAULT NULL, owner_id UUID NOT NULL, truck_number VARCHAR(20) NOT NULL, license_plate VARCHAR(20) NOT NULL, vin VARCHAR(17) NOT NULL, make VARCHAR(50) NOT NULL, model VARCHAR(50) NOT NULL, year INT NOT NULL, color VARCHAR(30) NOT NULL, truck_type VARCHAR(20) NOT NULL, fuel_type VARCHAR(20) NOT NULL, transmission_type VARCHAR(20) NOT NULL, daily_rate NUMERIC(10, 2) NOT NULL, odometer INT NOT NULL, fuel_capacity NUMERIC(5, 2) NOT NULL, max_payload INT NOT NULL, status VARCHAR(20) NOT NULL, condition VARCHAR(20) NOT NULL, last_inspection_date DATE DEFAULT NULL, next_inspection_date DATE DEFAULT NULL, insurance_expiry_date DATE DEFAULT NULL, registration_expiry_date DATE DEFAULT NULL, description TEXT DEFAULT NULL, notes TEXT DEFAULT NULL, location VARCHAR(200) DEFAULT NULL, specifications JSON DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FB16EAE6F017FF2F ON trucks (truck_number)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FB16EAE6F5AA79D0 ON trucks (license_plate)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FB16EAE6B1085141 ON trucks (vin)');
        $this->addSql('CREATE INDEX IDX_FB16EAE6979B1AD6 ON trucks (company_id)');
        $this->addSql('CREATE INDEX IDX_FB16EAE67E3C61F9 ON trucks (owner_id)');
        $this->addSql('COMMENT ON COLUMN trucks.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN trucks.company_id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN trucks.owner_id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN trucks.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN trucks.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE users (id UUID NOT NULL, company_id UUID DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, phone VARCHAR(20) DEFAULT NULL, profile_image TEXT DEFAULT NULL, user_role VARCHAR(20) NOT NULL, status VARCHAR(20) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, last_login_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE INDEX IDX_1483A5E9979B1AD6 ON users (company_id)');
        $this->addSql('COMMENT ON COLUMN users.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN users.company_id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN users.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN users.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN users.last_login_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE bookings ADD CONSTRAINT FK_7A853C35C6957CCE FOREIGN KEY (truck_id) REFERENCES trucks (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bookings ADD CONSTRAINT FK_7A853C35E289A545 FOREIGN KEY (renter_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE truck_documents ADD CONSTRAINT FK_D7B61030C6957CCE FOREIGN KEY (truck_id) REFERENCES trucks (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE truck_images ADD CONSTRAINT FK_86985E11C6957CCE FOREIGN KEY (truck_id) REFERENCES trucks (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trucks ADD CONSTRAINT FK_FB16EAE6979B1AD6 FOREIGN KEY (company_id) REFERENCES companies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trucks ADD CONSTRAINT FK_FB16EAE67E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9979B1AD6 FOREIGN KEY (company_id) REFERENCES companies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE bookings DROP CONSTRAINT FK_7A853C35C6957CCE');
        $this->addSql('ALTER TABLE bookings DROP CONSTRAINT FK_7A853C35E289A545');
        $this->addSql('ALTER TABLE truck_documents DROP CONSTRAINT FK_D7B61030C6957CCE');
        $this->addSql('ALTER TABLE truck_images DROP CONSTRAINT FK_86985E11C6957CCE');
        $this->addSql('ALTER TABLE trucks DROP CONSTRAINT FK_FB16EAE6979B1AD6');
        $this->addSql('ALTER TABLE trucks DROP CONSTRAINT FK_FB16EAE67E3C61F9');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E9979B1AD6');
        $this->addSql('DROP TABLE bookings');
        $this->addSql('DROP TABLE companies');
        $this->addSql('DROP TABLE truck_documents');
        $this->addSql('DROP TABLE truck_images');
        $this->addSql('DROP TABLE trucks');
        $this->addSql('DROP TABLE users');
    }
}
