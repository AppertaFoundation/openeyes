<?php

class m150505_100654_removeBiometryDataElement extends CDbMigration
{
    public function up()
    {
        $this->delete('element_type', "class_name = 'Element_OphInBiometry_BiometryData'");
    }

    public function down()
    {
        $et = $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :cn', array(':cn' => 'OphInBiometry'))->queryRow();

        $this->insert('element_type', array(
            'event_type_id' => $et['id'],
            'name' => 'Biometry Data',
            'class_name' => 'Element_OphInBiometry_BiometryData',
            'display_order' => 30,
            'default' => 1,
        ));
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
