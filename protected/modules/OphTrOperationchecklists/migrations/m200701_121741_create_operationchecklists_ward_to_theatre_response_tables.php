<?php

class m200701_121741_create_operationchecklists_ward_to_theatre_response_tables extends OEMigration
{
    public function up()
    {
        // documentation
        $this->createOETable('ophtroperationchecklists_documentation_results', array(
            'id' => 'pk',
            'element_id' => 'int(11)',
            'question_id' => 'int(11)',
            'answer_id' => 'int(11) NULL',
            'answer' => 'text',
            'comment' => 'text',
            'set_id' => 'int(11)'
        ), true);

        $this->addForeignKey(
            'ophtroperationchecklists_documentation_results_qid_fk',
            'ophtroperationchecklists_documentation_results',
            'question_id',
            'ophtroperationchecklists_questions',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationchecklists_documentation_results_aid_fk',
            'ophtroperationchecklists_documentation_results',
            'answer_id',
            'ophtroperationchecklists_answers',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationchecklists_documentation_results_eid_fk',
            'ophtroperationchecklists_documentation_results',
            'element_id',
            'et_ophtroperationchecklists_documentation',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationchecklists_documentation_results_esid_fk',
            'ophtroperationchecklists_documentation_results',
            'set_id',
            'ophtroperationchecklists_element_set',
            'id'
        );

        // Clinical assessment
        $this->createOETable('ophtroperationchecklists_clinical_results', array(
            'id' => 'pk',
            'element_id' => 'int(11)',
            'question_id' => 'int(11)',
            'answer_id' => 'int(11) NULL',
            'answer' => 'text',
            'comment' => 'text',
            'set_id' => 'int(11)'
        ), true);

        $this->addForeignKey(
            'ophtroperationchecklists_clinical_results_qid_fk',
            'ophtroperationchecklists_clinical_results',
            'question_id',
            'ophtroperationchecklists_questions',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationchecklists_clinical_results_aid_fk',
            'ophtroperationchecklists_clinical_results',
            'answer_id',
            'ophtroperationchecklists_answers',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationchecklists_clinical_results_eid_fk',
            'ophtroperationchecklists_clinical_results',
            'element_id',
            'et_ophtroperationchecklists_clinical_assessment',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationchecklists_clinical_results_esid_fk',
            'ophtroperationchecklists_clinical_results',
            'set_id',
            'ophtroperationchecklists_element_set',
            'id'
        );

        // Nursing/Practitioner Assessment
        $this->createOETable('ophtroperationchecklists_nursing_results', array(
            'id' => 'pk',
            'element_id' => 'int(11)',
            'question_id' => 'int(11)',
            'answer_id' => 'int(11) NULL',
            'answer' => 'text',
            'comment' => 'text',
            'set_id' => 'int(11)'
        ), true);

        $this->addForeignKey(
            'ophtroperationchecklists_nursing_results_qid_fk',
            'ophtroperationchecklists_nursing_results',
            'question_id',
            'ophtroperationchecklists_questions',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationchecklists_nursing_results_aid_fk',
            'ophtroperationchecklists_nursing_results',
            'answer_id',
            'ophtroperationchecklists_answers',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationchecklists_nursing_results_eid_fk',
            'ophtroperationchecklists_nursing_results',
            'element_id',
            'et_ophtroperationchecklists_nursing_assessment',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationchecklists_nursing_results_esid_fk',
            'ophtroperationchecklists_nursing_results',
            'set_id',
            'ophtroperationchecklists_element_set',
            'id'
        );

        // DVT
        $this->createOETable('ophtroperationchecklists_dvt_results', array(
            'id' => 'pk',
            'element_id' => 'int(11)',
            'question_id' => 'int(11)',
            'answer_id' => 'int(11) NULL',
            'answer' => 'text',
            'comment' => 'text',
            'set_id' => 'int(11)'
        ), true);

        $this->addForeignKey(
            'ophtroperationchecklists_dvt_results_qid_fk',
            'ophtroperationchecklists_dvt_results',
            'question_id',
            'ophtroperationchecklists_questions',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationchecklists_dvt_results_aid_fk',
            'ophtroperationchecklists_dvt_results',
            'answer_id',
            'ophtroperationchecklists_answers',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationchecklists_dvt_results_eid_fk',
            'ophtroperationchecklists_dvt_results',
            'element_id',
            'et_ophtroperationchecklists_dvt',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationchecklists_dvt_results_esid_fk',
            'ophtroperationchecklists_dvt_results',
            'set_id',
            'ophtroperationchecklists_element_set',
            'id'
        );

        // Patient Support
        $this->createOETable('ophtroperationchecklists_patient_support_results', array(
            'id' => 'pk',
            'element_id' => 'int(11)',
            'question_id' => 'int(11)',
            'answer_id' => 'int(11) NULL',
            'answer' => 'text',
            'comment' => 'text',
            'set_id' => 'int(11)'
        ), true);

        $this->addForeignKey(
            'ophtroperationchecklists_patient_support_results_qid_fk',
            'ophtroperationchecklists_patient_support_results',
            'question_id',
            'ophtroperationchecklists_questions',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationchecklists_patient_support_results_aid_fk',
            'ophtroperationchecklists_patient_support_results',
            'answer_id',
            'ophtroperationchecklists_answers',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationchecklists_patient_support_results_eid_fk',
            'ophtroperationchecklists_patient_support_results',
            'element_id',
            'et_ophtroperationchecklists_patient_support',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationchecklists_patient_support_results_esid_fk',
            'ophtroperationchecklists_patient_support_results',
            'set_id',
            'ophtroperationchecklists_element_set',
            'id'
        );
    }

    public function down()
    {
        $this->dropOETable('ophtroperationchecklists_patient_support_results', true);
        $this->dropOETable('ophtroperationchecklists_dvt_results', true);
        $this->dropOETable('ophtroperationchecklists_nursing_results', true);
        $this->dropOETable('ophtroperationchecklists_clinical_results', true);
        $this->dropOETable('ophtroperationchecklists_documentation_results', true);
    }
}
