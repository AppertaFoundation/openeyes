<?php

class m200705_015948_create_operationchecklists_discharge_response_table extends OEMigration
{
    public function up()
    {
        $this->createOETable('ophtroperationchecklists_discharge_results', array(
            'id' => 'pk',
            'element_id' => 'int(11)',
            'question_id' => 'int(11)',
            'answer_id' => 'int(11) NULL',
            'answer' => 'text',
            'comment' => 'text',
        ), true);

        $this->addForeignKey(
            'ophtroperationchecklists_discharge_results_qid_fk',
            'ophtroperationchecklists_discharge_results',
            'question_id',
            'ophtroperationchecklists_questions',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationchecklists_discharge_results_aid_fk',
            'ophtroperationchecklists_discharge_results',
            'answer_id',
            'ophtroperationchecklists_answers',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationchecklists_discharge_results_eid_fk',
            'ophtroperationchecklists_discharge_results',
            'element_id',
            'et_ophtroperationchecklists_discharge',
            'id'
        );
    }

    public function down()
    {
        $this->dropOETable('ophtroperationchecklists_discharge_results', true);
    }
}
