<?php

class m200430_011554_create_operationchecklists_answer_table extends OEMigration
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
            'id' => 10,
            'answer' => 'Nasal cannula',
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
            'id' => 18,
            'answer' => 'Yesterday',
            'display_order' => 180
        ),
        array(
            'id' => 19,
            'answer' => 'Today',
            'display_order' => 190
        ),
        array(
            'id' => 20,
            'answer' => 'Appointment given',
            'display_order' => 200
        ),
        array(
            'id' => 21,
            'answer' => 'Appointment to be made later',
            'display_order' => 210
        ),
        array(
            'id' => 22,
            'answer' => 'Discharged',
            'display_order' => 220
        ),
    );

    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Creating Table
        $this->createOETable(
            'ophtroperationchecklists_answers',
            array(
                'id' => 'pk',
                'answer' => 'text NOT NULL',
                'display_order' => 'int NOT NULL'
            ),
            true
        );

        $this->insertMultiple('ophtroperationchecklists_answers', $this->answers);
    }

    public function safeDown()
    {
        $this->dropOETable('ophtroperationchecklists_answers', true);
    }
}
