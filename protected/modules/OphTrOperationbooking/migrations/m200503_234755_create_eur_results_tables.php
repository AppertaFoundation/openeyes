<?php

class m200503_234755_create_eur_results_tables extends OEMigration
{
    public function up()
    {
        // create eur_event_result table
        $this->createOETable(
            'eur_event_results',
            array(
                'id' => 'pk',
                'result' => 'tinyint not null',
                'event_id' => 'int(10) unsigned not null',
                // eye_num is for first eye or second eye
                'eye_num' => 'tinyint not null',
                'eye_side' => 'int(10) unsigned',
                'CONSTRAINT fk_eur_event_result_event FOREIGN KEY (event_id) REFERENCES event(id)',
                'CONSTRAINT fk_eur_event_result_eye FOREIGN KEY (eye_side) REFERENCES eye(id)'
            ),
            true
        );
        // create eur_answer_results table
        $this->createOETable(
            'eur_answer_results',
            array(
                'id' => 'pk',
                'res_id' => 'int not null',
                'question_id' => 'int not null',
                'answer_id' => 'int not null',
                // eye_num is for first eye or second eye
                'eye_num' => 'tinyint not null',
                'eye_side' => 'int(10) unsigned',
                'CONSTRAINT fk_eur_answer_result_answer FOREIGN KEY (answer_id) REFERENCES eur_answers(id)',
                'CONSTRAINT fk_eur_answer_result_question FOREIGN KEY (question_id) REFERENCES eur_questions(id)',
                'CONSTRAINT fk_eur_answer_res_event_res FOREIGN KEY (res_id) REFERENCES eur_event_results(id)',
                'CONSTRAINT fk_eur_answer_result_eye FOREIGN KEY (eye_side) REFERENCES eye(id)'
            ),
            true
        );
    }

    public function down()
    {
        $this->dropOEtable('eur_answer_results', true);
        $this->dropOEtable('eur_event_results', true);
    }
}
