<?php

class m210104_031525_set_default_institution_id_in_exam_admin extends OEMigration
{
    public function up()
    {
        $institution_id = $this->dbConnection
            ->createCommand("SELECT * FROM institution WHERE remote_id = '" . Yii::app()->params['institution_code'] . "'")
            ->queryScalar();

        // Element Attributes
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_attribute SET institution_id = :institution_id WHERE institution_id IS NULL")
            ->execute(array(':institution_id' => $institution_id));

        // Workflows + rules
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_workflow SET institution_id = :institution_id WHERE institution_id IS NULL")
            ->execute(array(':institution_id' => $institution_id));

        // Required Allergy Assignment
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_allergy_set SET institution_id = :institution_id WHERE institution_id IS NULL")
            ->execute(array(':institution_id' => $institution_id));

        // Required Pupillary Abnormalities Set
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_pupillary_abnormality_set SET institution_id = :institution_id WHERE institution_id IS NULL")
            ->execute(array(':institution_id' => $institution_id));

        // Required risks assignment
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_risk_set SET institution_id = :institution_id WHERE institution_id IS NULL")
            ->execute(array(':institution_id' => $institution_id));

        // systemic diagnoses
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_systemic_diagnoses_set SET institution_id = :institution_id WHERE institution_id IS NULL")
            ->execute(array(':institution_id' => $institution_id));

        // Required Ophthalmic Surgical History
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_surgical_history_set SET institution_id = :institution_id WHERE institution_id IS NULL")
            ->execute(array(':institution_id' => $institution_id));

        // Required Systemic Surgical History Sets
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_systemic_surgery_set SET institution_id = :institution_id WHERE institution_id IS NULL")
            ->execute(array(':institution_id' => $institution_id));

        // Follow-up Statuses/Clinical Outcome Statuses
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_clinicoutcome_status SET institution_id = :institution_id WHERE institution_id IS NULL")
            ->execute(array(':institution_id' => $institution_id));

        // Follow-up Roles / Clinic Outcome Roles
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_clinicoutcome_role SET institution_id = :institution_id WHERE institution_id IS NULL")
            ->execute(array(':institution_id' => $institution_id));

        // Common post-op complications
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_postop_complications_subspecialty SET institution_id = :institution_id WHERE institution_id IS NULL")
            ->execute(array(':institution_id' => $institution_id));

        // Visit Intervals
        $this->dbConnection
            ->createCommand("UPDATE ophciexamination_visitinterval SET institution_id = :institution_id WHERE institution_id IS NULL")
            ->execute(array(':institution_id' => $institution_id));
    }

    public function down()
    {
        echo("This migration does not support Down");
    }
}
