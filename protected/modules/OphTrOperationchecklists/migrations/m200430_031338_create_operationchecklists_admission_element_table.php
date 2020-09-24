<?php

class m200430_031338_create_operationchecklists_admission_element_table extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Creating Table
        $this->createOETable(
            'et_ophtroperationchecklists_admission',
            array(
                'id' => 'pk',
                'event_id' => 'int unsigned NOT NULL',
            ),
            true
        );

        // Add Foreign Key
        $this->addForeignKey(
            'et_ophtroperationchecklists_admission_ev_fk',
            'et_ophtroperationchecklists_admission',
            'event_id',
            'event',
            'id'
        );

        $this->createOETable('ophtroperationchecklists_admission_results', array(
            'id' => 'pk',
            'element_id' => 'int(11)',
            'question_id' => 'int(11)',
            'answer_id' => 'int(11) NULL',
            'answer' => 'text',
            'comment' => 'text',
        ), true);

        $this->addForeignKey(
            'ophtroperationchecklists_ar_qid_fk',
            'ophtroperationchecklists_admission_results',
            'question_id',
            'ophtroperationchecklists_questions',
            'id'
        );
        $this->addForeignKey(
            'ophtroperationchecklists_ar_aid_fk',
            'ophtroperationchecklists_admission_results',
            'answer_id',
            'ophtroperationchecklists_answers',
            'id'
        );
        $this->addForeignKey(
            'ophtroperationchecklists_ar_eid_fk',
            'ophtroperationchecklists_admission_results',
            'element_id',
            'et_ophtroperationchecklists_admission',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropOETable('ophtroperationchecklists_admission_results', true);
        $this->dropOETable('et_ophtroperationchecklists_admission', true);
    }
}
