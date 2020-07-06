<?php

class m200705_015947_create_discharge_checklist_response_table extends OEMigration
{
    public function up()
    {
        $this->createOETable('ophcitheatreadmission_discharge_checklist_results', array(
            'id' => 'pk',
            'element_id' => 'int(11)',
            'question_id' => 'int(11)',
            'answer_id' => 'int(11) NULL',
            'answer' => 'text',
            'comment' => 'text',
        ), true);

        $this->addForeignKey(
            'ophcitheatreadmission_dischagecr_qid_fk',
            'ophcitheatreadmission_discharge_checklist_results',
            'question_id',
            'ophcitheatreadmission_checklist_questions',
            'id'
        );

        $this->addForeignKey(
            'ophcitheatreadmission_dischagecr_aid_fk',
            'ophcitheatreadmission_discharge_checklist_results',
            'answer_id',
            'ophcitheatreadmission_checklist_answers',
            'id'
        );

        $this->addForeignKey(
            'ophcitheatreadmission_dischagecr_eid_fk',
            'ophcitheatreadmission_discharge_checklist_results',
            'element_id',
            'et_ophcitheatreadmission_discharge',
            'id'
        );
    }

    public function down()
    {
        $this->dropOETable('ophcitheatreadmission_discharge_checklist_results', true);
    }
}
