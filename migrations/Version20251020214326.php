<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251020214326 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE buyers (id VARCHAR(50) NOT NULL, name VARCHAR(200) NOT NULL, sectors CLOB NOT NULL --(DC2Type:json)
        , location CLOB NOT NULL --(DC2Type:json)
        , demand_profile CLOB NOT NULL --(DC2Type:json)
        , min_quality_grade VARCHAR(20) NOT NULL, accepted_types CLOB NOT NULL --(DC2Type:json)
        , avg_price_per_kg NUMERIC(8, 4) DEFAULT NULL, certifications CLOB NOT NULL --(DC2Type:json)
        , is_active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE emission_factors (id VARCHAR(50) NOT NULL, material_type VARCHAR(100) NOT NULL, kg_co2e_per_kg NUMERIC(8, 4) NOT NULL, source VARCHAR(200) NOT NULL, description CLOB DEFAULT NULL, created_at DATETIME NOT NULL, is_active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE kpi_snapshots (id VARCHAR(50) NOT NULL, loop_id VARCHAR(50) NOT NULL, taken_at DATETIME NOT NULL, throughput_tons NUMERIC(10, 2) NOT NULL, circularity_score NUMERIC(5, 2) NOT NULL, co2e_saved NUMERIC(12, 2) NOT NULL, active_memberships INTEGER NOT NULL, notes CLOB DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_A1A8560C1851842 FOREIGN KEY (loop_id) REFERENCES loops (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_A1A8560C1851842 ON kpi_snapshots (loop_id)');
        $this->addSql('CREATE TABLE loop_memberships (id VARCHAR(50) NOT NULL, loop_id VARCHAR(50) NOT NULL, product_id VARCHAR(50) NOT NULL, buyer_id VARCHAR(50) NOT NULL, reserved_at DATETIME NOT NULL, status VARCHAR(20) NOT NULL, allocation_kg NUMERIC(10, 2) NOT NULL, locked_kpi_snapshot CLOB NOT NULL --(DC2Type:json)
        , match_score NUMERIC(5, 2) DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_A7E1E7D8C1851842 FOREIGN KEY (loop_id) REFERENCES loops (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A7E1E7D84584665A FOREIGN KEY (product_id) REFERENCES recycled_products (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A7E1E7D86C755722 FOREIGN KEY (buyer_id) REFERENCES buyers (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_A7E1E7D8C1851842 ON loop_memberships (loop_id)');
        $this->addSql('CREATE INDEX IDX_A7E1E7D84584665A ON loop_memberships (product_id)');
        $this->addSql('CREATE INDEX IDX_A7E1E7D86C755722 ON loop_memberships (buyer_id)');
        $this->addSql('CREATE TABLE loops (id VARCHAR(50) NOT NULL, name VARCHAR(200) NOT NULL, objective CLOB NOT NULL, required_types CLOB NOT NULL --(DC2Type:json)
        , min_quality_grade VARCHAR(20) NOT NULL, preferred_buyers CLOB NOT NULL --(DC2Type:json)
        , region VARCHAR(100) NOT NULL, circularity_weighting CLOB NOT NULL --(DC2Type:json)
        , kpis CLOB NOT NULL --(DC2Type:json)
        , max_capacity_kg NUMERIC(10, 2) DEFAULT NULL, is_active BOOLEAN NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE recycled_products (id VARCHAR(50) NOT NULL, type VARCHAR(100) NOT NULL, quality_grade VARCHAR(20) NOT NULL, quantity_kg NUMERIC(10, 2) NOT NULL, batch_code VARCHAR(100) NOT NULL, produced_at DATETIME NOT NULL, facility_id VARCHAR(50) NOT NULL, emission_factor_kg_co2e_per_kg NUMERIC(8, 4) DEFAULT NULL, metadata CLOB NOT NULL --(DC2Type:json)
        , is_committed BOOLEAN NOT NULL, committed_quantity_kg NUMERIC(10, 2) DEFAULT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE buyers');
        $this->addSql('DROP TABLE emission_factors');
        $this->addSql('DROP TABLE kpi_snapshots');
        $this->addSql('DROP TABLE loop_memberships');
        $this->addSql('DROP TABLE loops');
        $this->addSql('DROP TABLE recycled_products');
    }
}
