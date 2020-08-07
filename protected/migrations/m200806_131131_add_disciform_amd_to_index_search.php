<?php

class m200806_131131_add_disciform_amd_to_index_search extends CDbMigration
{
    public function safeUp()
    {
        $examination_macula = $this->dbConnection->createCommand('SELECT id, open_element_class_name FROM index_search WHERE primary_term = "Examination Macula"')->queryRow();
        $examination_event_type_id = $this->dbConnection->createCommand('SELECT id FROM event_type WHERE name = "Examination"')->queryScalar();
        if ($examination_macula) {
            $this->insert('index_search', [
                'event_type_id' => $examination_event_type_id,
                'parent' => $examination_macula['id'],
                'primary_term' => 'Disciform AMD',
                'open_element_class_name' => $examination_macula['open_element_class_name'],
                'goto_doodle_class_name' => 'Fovea',
                'goto_property' => 'Type'
            ]);
        }
    }

    public function safeDown()
    {
        $this->delete('index_search', 'primary_term = ?', ['Disciform AMD']);
    }
}
