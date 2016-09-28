<?php

class m140411_152400_namespacing extends CDbMigration
{
    public function up()
    {
        $namespace = 'OEModule\\OphCiExamination';
        $event_type = $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphCiExamination'))->queryRow();

        $elements = $this->dbConnection->createCommand()->select('*')->from('element_type')->where('event_type_id = :eid', array(':eid' => $event_type['id']))->queryAll();

        foreach ($elements as $element) {
            $this->update('element_type', array('class_name' => $namespace.'\\models\\'.$element['class_name']), 'id = :eid', array(':eid' => $element['id']));
        }
    }

    public function down()
    {
        $event_type = $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphCiExamination'))->queryRow();

        $elements = $this->dbConnection->createCommand()->select('*')->from('element_type')->where('event_type_id = :eid', array(':eid' => $event_type['id']))->queryAll();

        foreach ($elements as $element) {
            $ns_components = explode('\\', $element['class_name']);
            $this->update('element_type', array('class_name' => end($ns_components)), 'id = :eid', array(':eid' => $element['id']));
        }
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
