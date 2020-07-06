<?php

class m200701_121740_create_ward_to_theatre_checklist_response_tables extends OEMigration
{
    public function up()
    {
        // documentation
        $this->createOETable('ophcitheatreadmission_documentation_checklist_results', array(
            'id' => 'pk',
            'element_id' => 'int(11)',
            'question_id' => 'int(11)',
            'answer_id' => 'int(11) NULL',
            'answer' => 'text',
            'comment' => 'text',
            'set_id' => 'int(11)'
        ));

        $this->addForeignKey(
            'ophcitheatreadmission_doccr_qid_fk',
            'ophcitheatreadmission_documentation_checklist_results',
            'question_id',
            'ophcitheatreadmission_checklist_questions',
            'id'
        );

        $this->addForeignKey(
            'ophcitheatreadmission_doccr_aid_fk',
            'ophcitheatreadmission_documentation_checklist_results',
            'answer_id',
            'ophcitheatreadmission_checklist_answers',
            'id'
        );

        $this->addForeignKey(
            'ophcitheatreadmission_doccr_eid_fk',
            'ophcitheatreadmission_documentation_checklist_results',
            'element_id',
            'et_ophcitheatreadmission_documentation',
            'id'
        );

        $this->addForeignKey(
            'ophcitheatreadmission_doccr_esid_fk',
            'ophcitheatreadmission_documentation_checklist_results',
            'set_id',
            'ophcitheatreadmission_element_set',
            'id'
        );

        // Clinical assessment
        $this->createOETable('ophcitheatreadmission_clinical_checklist_results', array(
            'id' => 'pk',
            'element_id' => 'int(11)',
            'question_id' => 'int(11)',
            'answer_id' => 'int(11) NULL',
            'answer' => 'text',
            'comment' => 'text',
            'set_id' => 'int(11)'
        ));

        $this->addForeignKey(
            'ophcitheatreadmission_ccr_qid_fk',
            'ophcitheatreadmission_clinical_checklist_results',
            'question_id',
            'ophcitheatreadmission_checklist_questions',
            'id'
        );

        $this->addForeignKey(
            'ophcitheatreadmission_ccr_aid_fk',
            'ophcitheatreadmission_clinical_checklist_results',
            'answer_id',
            'ophcitheatreadmission_checklist_answers',
            'id'
        );

        $this->addForeignKey(
            'ophcitheatreadmission_ccr_eid_fk',
            'ophcitheatreadmission_clinical_checklist_results',
            'element_id',
            'et_ophcitheatreadmission_clinical_assessment',
            'id'
        );

        $this->addForeignKey(
            'ophcitheatreadmission_ccr_esid_fk',
            'ophcitheatreadmission_clinical_checklist_results',
            'set_id',
            'ophcitheatreadmission_element_set',
            'id'
        );

        // Nursing/Practitioner Assessment
        $this->createOETable('ophcitheatreadmission_nursing_checklist_results', array(
            'id' => 'pk',
            'element_id' => 'int(11)',
            'question_id' => 'int(11)',
            'answer_id' => 'int(11) NULL',
            'answer' => 'text',
            'comment' => 'text',
            'set_id' => 'int(11)'
        ));

        $this->addForeignKey(
            'ophcitheatreadmission_ncr_qid_fk',
            'ophcitheatreadmission_nursing_checklist_results',
            'question_id',
            'ophcitheatreadmission_checklist_questions',
            'id'
        );

        $this->addForeignKey(
            'ophcitheatreadmission_ncr_aid_fk',
            'ophcitheatreadmission_nursing_checklist_results',
            'answer_id',
            'ophcitheatreadmission_checklist_answers',
            'id'
        );

        $this->addForeignKey(
            'ophcitheatreadmission_ncr_eid_fk',
            'ophcitheatreadmission_nursing_checklist_results',
            'element_id',
            'et_ophcitheatreadmission_nursing_assessment',
            'id'
        );

        $this->addForeignKey(
            'ophcitheatreadmission_ncr_esid_fk',
            'ophcitheatreadmission_nursing_checklist_results',
            'set_id',
            'ophcitheatreadmission_element_set',
            'id'
        );

        // DVT
        $this->createOETable('ophcitheatreadmission_dvt_checklist_results', array(
            'id' => 'pk',
            'element_id' => 'int(11)',
            'question_id' => 'int(11)',
            'answer_id' => 'int(11) NULL',
            'answer' => 'text',
            'comment' => 'text',
            'set_id' => 'int(11)'
        ));

        $this->addForeignKey(
            'ophcitheatreadmission_dcr_qid_fk',
            'ophcitheatreadmission_dvt_checklist_results',
            'question_id',
            'ophcitheatreadmission_checklist_questions',
            'id'
        );

        $this->addForeignKey(
            'ophcitheatreadmission_dcr_aid_fk',
            'ophcitheatreadmission_dvt_checklist_results',
            'answer_id',
            'ophcitheatreadmission_checklist_answers',
            'id'
        );

        $this->addForeignKey(
            'ophcitheatreadmission_dcr_eid_fk',
            'ophcitheatreadmission_dvt_checklist_results',
            'element_id',
            'et_ophcitheatreadmission_dvt',
            'id'
        );

        $this->addForeignKey(
            'ophcitheatreadmission_dcr_esid_fk',
            'ophcitheatreadmission_dvt_checklist_results',
            'set_id',
            'ophcitheatreadmission_element_set',
            'id'
        );

        // Patient Support
        $this->createOETable('ophcitheatreadmission_patient_support_checklist_results', array(
            'id' => 'pk',
            'element_id' => 'int(11)',
            'question_id' => 'int(11)',
            'answer_id' => 'int(11) NULL',
            'answer' => 'text',
            'comment' => 'text',
            'set_id' => 'int(11)'
        ));

        $this->addForeignKey(
            'ophcitheatreadmission_pscr_qid_fk',
            'ophcitheatreadmission_patient_support_checklist_results',
            'question_id',
            'ophcitheatreadmission_checklist_questions',
            'id'
        );

        $this->addForeignKey(
            'ophcitheatreadmission_pscr_aid_fk',
            'ophcitheatreadmission_patient_support_checklist_results',
            'answer_id',
            'ophcitheatreadmission_checklist_answers',
            'id'
        );

        $this->addForeignKey(
            'ophcitheatreadmission_pscr_eid_fk',
            'ophcitheatreadmission_patient_support_checklist_results',
            'element_id',
            'et_ophcitheatreadmission_patient_support',
            'id'
        );

        $this->addForeignKey(
            'ophcitheatreadmission_pscr_esid_fk',
            'ophcitheatreadmission_patient_support_checklist_results',
            'set_id',
            'ophcitheatreadmission_element_set',
            'id'
        );
    }

    public function down()
    {
        $this->dropOETable('ophcitheatreadmission_patient_support_checklist_results');
        $this->dropOETable('ophcitheatreadmission_dvt_checklist_results');
        $this->dropOETable('ophcitheatreadmission_nursing_checklist_results');
        $this->dropOETable('ophcitheatreadmission_clinical_checklist_results');
        $this->dropOETable('ophcitheatreadmission_documentation_checklist_results');
    }
}
