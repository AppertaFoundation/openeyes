<?php

class m200430_031337_create_theatreadmissions_checklist extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Creating Table
        $this->createOETable(
            'et_ophcitheatreadmission_admission_checklist',
            array(
                'id' => 'pk',
                'event_id' => 'int unsigned NOT NULL',
            ),
            true
        );

        // Add Foreign Key
        $this->addForeignKey(
            'et_ophcitheatreadmission_checklist_ev_fk',
            'et_ophcitheatreadmission_admission_checklist',
            'event_id',
            'event',
            'id'
        );

        $this->createOETable('ophcitheatreadmission_admission_checklist_results', array(
            'id' => 'pk',
            'element_id' => 'int(11)',
            'question_id' => 'int(11)',
            'answer_id' => 'int(11) NULL',
            'answer' => 'text',
            'comment' => 'text',
        ));

        $this->addForeignKey(
            'ophcitheatreadmission_acr_qid_fk',
            'ophcitheatreadmission_admission_checklist_results',
            'question_id',
            'ophcitheatreadmission_checklist_questions',
            'id'
        );
        $this->addForeignKey(
            'ophcitheatreadmission_acr_aid_fk',
            'ophcitheatreadmission_admission_checklist_results',
            'answer_id',
            'ophcitheatreadmission_checklist_answers',
            'id'
        );
        $this->addForeignKey(
            'ophcitheatreadmission_acr_eid_fk',
            'ophcitheatreadmission_admission_checklist_results',
            'element_id',
            'et_ophcitheatreadmission_admission_checklist',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('ophcitheatreadmission_acr_eid_fk', 'cat_prom5_answer_results');
        $this->dropForeignKey('ophcitheatreadmission_acr_aid_fk', 'cat_prom5_answer_results');
        $this->dropForeignKey('ophcitheatreadmission_acr_qid_fk', 'cat_prom5_answer_results');
        $this->dropOETable('ophcitheatreadmission_admission_checklist_results', true);
        $this->dropOETable('et_ophcitheatreadmission_admission_checklist', true);
    }
}
