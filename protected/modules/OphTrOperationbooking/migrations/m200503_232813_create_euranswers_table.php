<?php

class m200503_232813_create_euranswers_table extends OEMigration
{
    public function up()
    {
        $questions = $this->dbConnection->createCommand('
            SELECT id
            FROM eur_questions
        ')->queryColumn();
        $this->createOETable(
            'eur_answers',
            array(
                'id' => 'pk',
                'question_id' => 'int not null',
                'answer' => 'text not null',
                // eye_num is for first eye or second eye
                'value' => 'tinyint not null',
                'constraint fk_eur_question_answer foreign key (question_id) references eur_questions (id)',
            ),
            true
        );
        foreach ($questions as $q) {
            $this->insert(
                'eur_answers',
                array(
                    'question_id' => $q,
                    'answer' => 'Yes',
                    'value' => '1'
                )
            );
            $this->insert(
                'eur_answers',
                array(
                    'question_id' => $q,
                    'answer' => 'No',
                    'value' => '0'
                )
            );
        }
    }

    public function down()
    {
        $this->dropOEtable('eur_questions', true);
    }
}
