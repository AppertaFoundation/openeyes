<?php

class m180216_093332_add_sc_injection_mgment_reason_for_no_treatment extends OEMigration
{
    public function up()
    {
        try {
            $this->registerShortcode(27, "eit", "getReasonForNoTreatmentFromLastExamination", "Reason for no treatment from last Examination/Clinical Management->Injection Management");
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }

        return true;
    }

    public function down()
    {
        $this->delete('patient_shortcode', "code = :code", array(':code' => 'eit'));
    }
}