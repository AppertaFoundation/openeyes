<?php

class m200430_011553_create_theatreadmissions_checklist_answer_table extends OEMigration
{
    public $answers = array (
        array (
            'id' => 1,
            'answer' => 'Yes',
            'display_order' => 10
        ),
        array (
            'id' => 2,
            'answer' => 'No',
            'display_order' => 20
        ),
        array (
            'id' => 3,
            'answer' => 'N/A',
            'display_order' => 30
        ),
        array(
            'id' => 4,
            'answer' => 'Right',
            'display_order' => 40
        ),
        array(
            'id' => 5,
            'answer' => 'Left',
            'display_order' => 50
        ),
        array(
            'id' => 6,
            'answer' => 'Bilateral',
            'display_order' => 60
        ),
        array(
            'id' => 7,
            'answer' => 'Positive',
            'display_order' => 70
        ),
        array(
            'id' => 8,
            'answer' => 'Negative',
            'display_order' => 80
        ),
        array(
            'id' => 9,
            'answer' => 'Swab Taken',
            'display_order' => 90
        ),
        array(
            'id' => 10,
            'answer' => 'Nasal Canal',
            'display_order' => 100
        ),
        array(
            'id' => 11,
            'answer' => 'Face Mask',
            'display_order' => 110
        ),
        array(
            'id' => 12,
            'answer' => 'Removed',
            'display_order' => 120
        ),
        array(
            'id' => 13,
            'answer' => 'Taped',
            'display_order' => 130
        ),
        array(
            'id' => 14,
            'answer' => 'D Grey',
            'display_order' => 140
        ),
        array(
            'id' => 15,
            'answer' => 'White',
            'display_order' => 150
        ),
        array(
            'id' => 16,
            'answer' => 'P Grey',
            'display_order' => 160
        ),
        array(
            'id' => 17,
            'answer' => 'Extended',
            'display_order' => 170
        ),
    );

    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Creating Table
        $this->createOETable(
            'ophcitheatreadmission_checklist_answers',
            array(
                'id' => 'pk',
                'answer' => 'text NOT NULL',
                'display_order' => 'int NOT NULL'
            ),
            true
        );

        foreach ($this->answers as $answer) {
            $this->insert('ophcitheatreadmission_checklist_answers', $answer);
        }
    }

    public function safeDown()
    {
        $this->dropOETable('ophcitheatreadmission_checklist_answers', true);
    }
}
