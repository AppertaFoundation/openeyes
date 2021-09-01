<?php

class m200501_071140_create_eurquestions_table extends OEMigration
{
    public function up()
    {
        $questions = array(
            array(
                'question' => 'The best corrected visual acuity score is worse than 6/9 (Snellen) or 0.2 (Logmar) in the affected eye AND the patient has difficulty carrying out everyday tasks such as recognising faces, watching TV, reading, cooking, playing sport/cards, etc.',
                'display_order' => 10,
                'eye_num' => 3,
            ),
            array(
                'question' => 'The patients visual acuity is 6/9 or better but they report excessive difficulty in twilight or dark conditions and the difficulty has been confirmed by a clinician to be the result of reduced contrast sensitivity.',
                'display_order' => 20,
                'eye_num' => 3,
            ),
            array(
                'question' => 'Binocular considerations/anisometropia/disabling glare.',
                'display_order' => 30,
                'eye_num' => 2,
            ),
        );
        $this->createOETable(
            'eur_questions',
            array(
                'id' => 'pk',
                'question' => 'text not null',
                'display_order' => 'int not null',
                // eye_num is for first eye or second eye
                'eye_num' => 'tinyint not null'
            ),
            true
        );
        $this->insertMultiple('eur_questions', $questions);
    }

    public function down()
    {
        $this->dropOEtable('eur_questions', true);
    }
}
