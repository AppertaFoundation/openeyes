<?php

class m200701_022321_create_theatre_admission_ward_to_theatre_elements extends OEMigration
{
    public function up()
    {
        // Creating Table
        $this->createOETable('et_ophcitheatreadmission_documentation', array(
            'id' => 'pk',
            'event_id' => 'int unsigned NOT NULL',
            ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'et_ophcitheatreadmission_doc_ev_fk',
            'et_ophcitheatreadmission_documentation',
            'event_id',
            'event',
            'id'
        );

        // Creating Table
        $this->createOETable('et_ophcitheatreadmission_clinical_assessment', array(
            'id' => 'pk',
            'event_id' => 'int unsigned NOT NULL',
        ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'et_ophcitheatreadmission_ca_ev_fk',
            'et_ophcitheatreadmission_clinical_assessment',
            'event_id',
            'event',
            'id'
        );

        // Creating Table
        $this->createOETable('et_ophcitheatreadmission_nursing_assessment', array(
            'id' => 'pk',
            'event_id' => 'int unsigned NOT NULL',
        ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'et_ophcitheatreadmission_na_ev_fk',
            'et_ophcitheatreadmission_nursing_assessment',
            'event_id',
            'event',
            'id'
        );

        // Creating Table
        $this->createOETable('et_ophcitheatreadmission_dvt', array(
            'id' => 'pk',
            'event_id' => 'int unsigned NOT NULL',
        ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'et_ophcitheatreadmission_dvt_ev_fk',
            'et_ophcitheatreadmission_dvt',
            'event_id',
            'event',
            'id'
        );

        // Creating Table
        $this->createOETable('et_ophcitheatreadmission_patient_support', array(
            'id' => 'pk',
            'event_id' => 'int unsigned NOT NULL',
        ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'et_ophcitheatreadmission_ps_ev_fk',
            'et_ophcitheatreadmission_patient_support',
            'event_id',
            'event',
            'id'
        );
    }

    public function down()
    {
        $this->dropOETable('et_ophcitheatreadmission_documentation', true);
        $this->dropOETable('et_ophcitheatreadmission_clinical_assessment', true);
        $this->dropOETable('et_ophcitheatreadmission_nursing_assessment', true);
        $this->dropOETable('et_ophcitheatreadmission_pressure_ulcer_prev_man', true);
        $this->dropOETable('et_ophcitheatreadmission_dvt', true);
        $this->dropOETable('et_ophcitheatreadmission_patient_support', true);
    }
}
