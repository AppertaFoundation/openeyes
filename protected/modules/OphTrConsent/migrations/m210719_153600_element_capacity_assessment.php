<?php

class m210719_153600_element_capacity_assessment extends OEMigration
{
    private const ARCHIVE_ET_CAP_ASSESSMENT = 'et_ophtrconsent_capacity_assessment_archive';
    private const ARCHIVE_ET_CAP_ASSESSMENT_V = 'et_ophtrconsent_capacity_assessment_version_archive';

    public function up()
    {

        if ($this->dbConnection->schema->getTable('et_ophtrconsent_capacity_assessment', true) === null) {
            $this->createOETable("et_ophtrconsent_capacity_assessment", array(
                'id' => 'pk',
                'event_id' => 'INT(10) UNSIGNED',
                'how_judgement_was_made' => 'TEXT',
                'evidence' => 'TEXT NULL',
                'attempts_to_assist' => 'TEXT NULL',
                'basis_of_decision' => 'TEXT NULL'
            ), true);

            $this->addForeignKey("fk_et_ophtrconsent_ca_event_id", "et_ophtrconsent_capacity_assessment", "event_id", "event", "id");

            $this->createElementType("OphTrConsent", 'Assessment of patient\'s capacity', array(
                'class_name' => 'OEModule\\OphTrConsent\\models\\Element_OphTrConsent_CapacityAssessment',
                'default' => true,
                'required' => true,
                'display_order' => 80
            ));
        } else {
            $this->execute("CREATE TABLE " . self::ARCHIVE_ET_CAP_ASSESSMENT . " AS SELECT * FROM et_ophtrconsent_capacity_assessment");
            $this->execute("CREATE TABLE " . self::ARCHIVE_ET_CAP_ASSESSMENT_V . " AS SELECT * FROM et_ophtrconsent_capacity_assessment_version");

            $this->execute("
                UPDATE et_ophtrconsent_capacity_assessment
                SET basis_of_decision = concat(basis_of_decision, CHAR(10), patient_impairment);
            ");

            $this->execute("
                UPDATE et_ophtrconsent_capacity_assessment_version
                SET basis_of_decision = concat(basis_of_decision, CHAR(10), patient_impairment);
            ");

            $this->execute('ALTER TABLE et_ophtrconsent_capacity_assessment
                            DROP COLUMN IF EXISTS patient_impairment,
                            DROP COLUMN IF EXISTS patient_has_capacity;');

            $this->execute('ALTER TABLE et_ophtrconsent_capacity_assessment_version
                            DROP COLUMN IF EXISTS patient_impairment,
                            DROP COLUMN IF EXISTS patient_has_capacity;');
        }
    }

    public function down()
    {
        echo "m210719_153600_element_capacity_assessment does not support migration down.\n";
        return false;
    }
}
