<?php

class m200701_022322_create_operationchecklists_ward_to_theatre_elements extends OEMigration
{
    public function up()
    {
        // Creating Table
        $this->createOETable('et_ophtroperationchecklists_documentation', array(
            'id' => 'pk',
            'event_id' => 'int unsigned NOT NULL',
            ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'et_ophtroperationchecklists_documentation_ev_fk',
            'et_ophtroperationchecklists_documentation',
            'event_id',
            'event',
            'id'
        );

        // Creating Table
        $this->createOETable('et_ophtroperationchecklists_clinical_assessment', array(
            'id' => 'pk',
            'event_id' => 'int unsigned NOT NULL',
        ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'et_ophtroperationchecklists_clinical_assessment_ev_fk',
            'et_ophtroperationchecklists_clinical_assessment',
            'event_id',
            'event',
            'id'
        );

        // Creating Table
        $this->createOETable('et_ophtroperationchecklists_nursing_assessment', array(
            'id' => 'pk',
            'event_id' => 'int unsigned NOT NULL',
        ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'et_ophtroperationchecklists_nursing_assessment_ev_fk',
            'et_ophtroperationchecklists_nursing_assessment',
            'event_id',
            'event',
            'id'
        );

        // Creating Table
        $this->createOETable('et_ophtroperationchecklists_dvt', array(
            'id' => 'pk',
            'event_id' => 'int unsigned NOT NULL',
        ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'et_ophtroperationchecklists_dvt_ev_fk',
            'et_ophtroperationchecklists_dvt',
            'event_id',
            'event',
            'id'
        );

        // Creating Table
        $this->createOETable('et_ophtroperationchecklists_patient_support', array(
            'id' => 'pk',
            'event_id' => 'int unsigned NOT NULL',
        ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'et_ophtroperationchecklists_patient_support_ev_fk',
            'et_ophtroperationchecklists_patient_support',
            'event_id',
            'event',
            'id'
        );
    }

    public function down()
    {
        $this->dropOETable('et_ophtroperationchecklists_patient_support', true);
        $this->dropOETable('et_ophtroperationchecklists_dvt', true);
        $this->dropOETable('et_ophtroperationchecklists_nursing_assessment', true);
        $this->dropOETable('et_ophtroperationchecklists_clinical_assessment', true);
        $this->dropOETable('et_ophtroperationchecklists_documentation', true);
    }
}
