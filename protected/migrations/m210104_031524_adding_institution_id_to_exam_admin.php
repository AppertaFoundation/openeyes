<?php

class m210104_031524_adding_institution_id_to_exam_admin extends OEMigration
{
    public function up()
    {
        $institution_id = $this->dbConnection
            ->createCommand("SELECT * FROM institution WHERE remote_id = '" . Yii::app()->params['institution_code'] . "'")
            ->queryScalar();

        // Element Attributes
        $this->addOEColumn('ophciexamination_attribute', 'institution_id', 'int(10) unsigned AFTER label', true);
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_attribute SET institution_id = :institution_id")
            ->execute(array(':institution_id' => $institution_id));

        // Workflows + rules
        $this->addOEColumn('ophciexamination_workflow', 'institution_id', 'int(10) unsigned AFTER name', true);
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_workflow SET institution_id = :institution_id")
            ->execute(array(':institution_id' => $institution_id));
        $this->addOEColumn('ophciexamination_workflow_rule', 'institution_id', 'int(10) unsigned', true);
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_workflow_rule SET institution_id = :institution_id")
            ->execute(array(':institution_id' => $institution_id));

        // Required Allergy Assignment
        $this->addOEColumn('ophciexamination_allergy_set', 'institution_id', 'int(10) unsigned', true);
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_allergy_set SET institution_id = :institution_id")
            ->execute(array(':institution_id' => $institution_id));

        // Required Pupillary Abnormalities Set
        $this->alterOEColumn('ophciexamination_pupillaryabnormalities_abnormality', 'active', 'tinyint(1) unsigned not null AFTER name');
        $this->addOEColumn('ophciexamination_pupillary_abnormality_set', 'institution_id', 'int(10) unsigned AFTER name', true);
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_pupillary_abnormality_set SET institution_id = :institution_id")
            ->execute(array(':institution_id' => $institution_id));

        // Required risks assignment
        $this->addOEColumn('ophciexamination_risk_set', 'institution_id', 'int(10) unsigned AFTER name', true);
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_risk_set SET institution_id = :institution_id")
            ->execute(array(':institution_id' => $institution_id));

        // systemic diagnoses
        $this->addOEColumn('ophciexamination_systemic_diagnoses_set', 'institution_id', 'int(10) unsigned AFTER name', true);
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_systemic_diagnoses_set SET institution_id = :institution_id")
            ->execute(array(':institution_id' => $institution_id));

        // Required Ophthalmic Surgical History
        $this->addOEColumn('ophciexamination_surgical_history_set', 'institution_id', 'int(10) unsigned AFTER name', true);
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_surgical_history_set SET institution_id = :institution_id")
            ->execute(array(':institution_id' => $institution_id));

        // Required Systemic Surgical History Sets
        $this->addOEColumn('ophciexamination_systemic_surgery_set', 'institution_id', 'int(10) unsigned AFTER name', true);
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_systemic_surgery_set SET institution_id = :institution_id")
            ->execute(array(':institution_id' => $institution_id));

        // Follow-up Statuses/Clinical Outcome Statuses
        $this->addOEColumn('ophciexamination_clinicoutcome_status', 'institution_id', 'int(10) unsigned', true);
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_clinicoutcome_status SET institution_id = :institution_id")
            ->execute(array(':institution_id' => $institution_id));

        // Follow-up Roles / Clinic Outcome Roles
        $this->addOEColumn('ophciexamination_clinicoutcome_role', 'institution_id', 'int(10) unsigned', true);
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_clinicoutcome_role SET institution_id = :institution_id")
            ->execute(array(':institution_id' => $institution_id));

        // Common post-op complications
        $this->addOEColumn('ophciexamination_postop_complications_subspecialty', 'institution_id', 'int(10) unsigned AFTER id', true);
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_postop_complications_subspecialty SET institution_id = :institution_id")
            ->execute(array(':institution_id' => $institution_id));

        // Visit Intervals
        $this->addOEColumn('ophciexamination_visitinterval', 'institution_id', 'int(10) unsigned AFTER name', true);
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_visitinterval SET institution_id = :institution_id")
            ->execute(array(':institution_id' => $institution_id));
    }

    public function down()
    {
        // Element Attributes
        $this->dropOEColumn('ophciexamination_attribute_element', 'institution_id', true);

        // Workflows + rules
        $this->dropOEColumn('ophciexamination_workflow', 'institution_id', true);
        $this->dropOEColumn('ophciexamination_workflow_rule', 'institution_id', true);

        // Required Allergy Set
        $this->dropOEColumn('ophciexamination_allergy_set', 'institution_id', true);

        // Required Pupillary Abnormalities Set
        $this->alterOEColumn('ophciexamination_pupillaryabnormalities_abnormality', 'active', 'tinyint(1) unsigned not null AFTER created_date', true);
        $this->dropOEColumn('ophciexamination_pupillary_abnormality_set', 'institution_id', true);

        // Required risks assignment
        $this->dropOEColumn('ophciexamination_risk_set', 'institution_id', true);

        // systemic diagnoses
        $this->dropOEColumn('ophciexamination_systemic_diagnoses_set', 'institution_id', true);

        // Required Ophthalmic Surgical History
        $this->dropOEColumn('ophciexamination_surgical_history_set', 'institution_id', true);

        // Required Systemic Surgical History Sets
        $this->dropOEColumn('ophciexamination_systemic_surgery_set', 'institution_id', true);

        // Follow-up Statuses/Clinical Outcome Statuses
        $this->dropOEColumn('ophciexamination_clinicoutcome_status', 'institution_id', true);

        // Follow-up Roles / Clinic Outcome Roles
        $this->dropOEColumn('ophciexamination_clinicoutcome_role', 'institution_id', true);

        // Common post-op complications
        $this->dropOEColumn('ophciexamination_postop_complications_subspecialty', 'institution_id', true);

        // Visit Intervals
        $this->dropOEColumn('ophciexamination_visitinterval', 'institution_id', true);
    }
}
