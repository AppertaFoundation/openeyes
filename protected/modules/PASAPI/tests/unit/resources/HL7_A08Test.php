<?php

use OEModule\PASAPI\components\PasApiObserver;
use OEModule\PASAPI\resources\HL7_A08;
use OEModule\PASAPI\resources\HL7_Patient;
use OEModule\PASAPI\resources\HL7_Patient_Visit;
use OEModule\PASAPI\resources\HL7_Patient_Visit_Additional;
use OEModule\PASAPI\resources\HL7_Diagnosis;
use OEModule\PASAPI\resources\HL7_Procedure;
use OEModule\PASAPI\resources\HL7_UK_Additional;

class HL7_A08Test extends PHPUnit_Framework_TestCase
{
    function test_updateEmergencyCareVisit()
    {
        $hl7_a08 = new HL7_A08();

        $hl7_patient = new HL7_Patient(
            array(
                "nhs_number" => "1183168532",
                "patient_number" => "1009211"
            )
        );
        $hl7_a08->setPatient($hl7_patient);

        $hl7_patient_visit = new HL7_Patient_Visit(
            array(
                "point_of_care" => "AES", //RDCEC
                "room" => "WFT",
                "admit_source" => "pv_test3",
                "visit_number" => "pv_test4"
            )
        );

        $hl7_a08->setPatientVisit($hl7_patient_visit);

        $hl7_patient_visit_additional = new HL7_Patient_Visit_Additional(
            array(
                "reason_to_admit" => "pva_test1",
                "chief_complaint_code" => "pva_test2",
                "chief_complaint_description" => "pva_test3",
                "alternative_identifier" => "pva_test4",
                "referral_source_code" => "pva_test5",
                "patient_status_code" => "pva_test6"
            )
        );
        $hl7_a08->setPatientVisitAdditional($hl7_patient_visit_additional);

        $hl7_a08_diag1 = new HL7_Diagnosis(
            array(
                "identifier" => 1,
                "diagnosis_coding_method" => "diag_test1",
                "diagnosis_code_identifier" => "diag_test2",
                "diagnosis_code_text" => "diag_test3",
                "diagnosis_code_name_of_coding_system" => "diag_test4",
                "diagnosis_description" => "diag_test5",
                "diagnosis_date_time" => "diag_test6",
                "diagnosis_type" => "diag_test7",
                "clinician_id_number" => "diag_test8",
                "clinician_family_name" => "diag_test9",
                "clinician_given_name" => "diag_test10",
                "clinician_prefix" => "diag_test111",
                "clinician_degree" => "diag_test12"
            )
        );

        $hl7_a08_diag2 = new HL7_Diagnosis(
            array(
                "identifier" => 2,
                "diagnosis_coding_method" => "diag2_test1",
                "diagnosis_code_identifier" => "diag2_test2",
                "diagnosis_code_text" => "diag2_test3",
                "diagnosis_code_name_of_coding_system" => "diag2_test4",
                "diagnosis_description" => "diag2_test5",
                "diagnosis_date_time" => "diag2_test6",
                "diagnosis_type" => "diag2_test7",
                "clinician_id_number" => "diag2_test8",
                "clinician_family_name" => "diag2_test9",
                "clinician_given_name" => "diag2_test10",
                "clinician_prefix" => "diag2_test111",
                "clinician_degree" => "diag2_test12"
            )
        );

        $hl7_a08->addDiagnosis($hl7_a08_diag1);
        $hl7_a08->addDiagnosis($hl7_a08_diag2);

        $hl7_a08_proc1 = new HL7_Procedure(
            array(
               "identifier" => 1,
               "procedure_coding_method" => "proc_test1",
               "procedure_code_identifier" => "proc_test2",
               "procedure_code_text" => "proc_test3",
               "procedure_code_name_of_coding_system" => "proc_test4",
               "procedure_date_time" => "proc_test5",
               "procedure_functional_type" => "proc_test6",
               "investigation_or_treatment_clinician" => "proc_test7",
               "clinician_id_number" => "proc_test8",
               "clinician_family_name" => "proc_test9",
               "clinician_given_name" => "proc_test10",
               "clinician_prefix" => "proc_test11",
               "clinician_degree" => "proc_test12",

            )
        );

        $hl7_a08_proc2 = new HL7_Procedure(
            array(
                "identifier" => 2,
                "procedure_coding_method" => "proc2_test1",
                "procedure_code_identifier" => "proc2_test2",
                "procedure_code_text" => "proc2_test3",
                "procedure_code_name_of_coding_system" => "proc2_test4",
                "procedure_date_time" => "proc2_test5",
                "procedure_functional_type" => "proc2_test6",
                "investigation_or_treatment_clinician" => "proc2_test7",
                "clinician_id_number" => "proc2_test8",
                "clinician_family_name" => "proc2_test9",
                "clinician_given_name" => "proc2_test10",
                "clinician_prefix" => "proc2_test11",
                "clinician_degree" => "proc2_test12"
            )
        );

        $hl7_a08->addProcedure($hl7_a08_proc1);
        $hl7_a08->addProcedure($hl7_a08_proc2);

        $hl7_uk_additional = new HL7_UK_Additional(
            array(
                "identifier" => "uka_test1",
                "text" => "uka_test2",
                "name_of_coding_system" => "uka_test3"
            )
        );
        $hl7_a08->setUKAdditionalData($hl7_uk_additional);

        $pasapiobserver = new PasApiObserver();
        $this->assertTrue($pasapiobserver->updateEmergencyCareVisit($hl7_a08));
    }

    function test_updateEmergencyCareVisitWithEventId()
    {
        Yii::app()->session['selected_site_id'] = 1;
        Yii::app()->session['selected_institution_id'] = 1;
        Yii::app()->params['display_primary_number_usage_code'] = "LOCAL";
        Yii::app()->params['display_secondary_number_usage_code'] = "GLOBAL";

        $hl7_a08 = new HL7_A08();

        $hl7_a08->setDataFromEvent(3686638);


        $pasapiobserver = new PasApiObserver();
        $this->assertTrue($pasapiobserver->updateEmergencyCareVisit($hl7_a08));
    }
}
