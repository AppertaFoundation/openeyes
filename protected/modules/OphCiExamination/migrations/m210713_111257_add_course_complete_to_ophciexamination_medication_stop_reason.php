<?php

class m210713_111257_add_course_complete_to_ophciexamination_medication_stop_reason extends OEMigration
{
    public function safeUp()
    {
        $course_complete_stop_reason_id = $this->dbConnection->createCommand('SELECT id FROM ophciexamination_medication_stop_reason WHERE name = "Course complete"')->queryScalar();
        // checking if course complete has not been deleted by trusts, otherwise we add it back as is needed for v4+
        if (!$course_complete_stop_reason_id) {
            $this->insert('ophciexamination_medication_stop_reason', [
                'name' => 'Course complete',
                'active' => 1,
            ]);
        }
    }

    public function safeDown()
    {
        echo "m210713_111257_add_course_complete_to_ophciexamination_medication_stop_reason does not support migration down.\n";
        return false;
    }
}
