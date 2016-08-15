<?php

class m150529_125639_allergies_short_code extends CDbMigration
{
    public function up()
    {
        $eventTypeId = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name=:class_name', array(':class_name' => 'OphCiExamination'))
            ->queryScalar();

        $this->insert('patient_shortcode', array(
            'event_type_id' => $eventTypeId,
            'default_code' => 'aka',
            'code' => 'aka',
            'method' => 'getAllergies',
            'description' => 'List of patients allergies',
        ));
    }

    public function down()
    {
        $this->delete('patient_shortcode', 'default_code = "aka"');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
