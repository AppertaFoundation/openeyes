<?php

class m161216_122437_event_type_OphInDnasample extends CDbMigration
{
    public function up()
    {
        if ($this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInBloodsample'))->queryRow()) {
            $group = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('name=:name', array(':name' => 'Investigation events'))->queryRow();
            $rowID = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInBloodsample'))->queryRow();
            $this->update('event_type', array('class_name' => 'OphInDnasample', 'name' => 'DNA sample'), 'id = '.$rowID['id'].' AND event_group_id = '.$group['id']);
        }

        $event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInDnasample'))->queryRow();

        $element_type_row = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('class_name=:class_name', array(':class_name' => 'Element_OphInBloodsample_Sample'))->queryRow();
        $this->update('element_type', array( 'name' => 'Sample', 'class_name' => 'Element_OphInDnasample_Sample', 'event_type_id' => $event_type['id'], 'display_order' => 1), 'id = '.$element_type_row['id'].' AND '.$event_type['id']);

        $this->renameTable('ophinbloodsample_sample_type', 'ophindnasample_sample_type');
        $this->renameTable('et_ophinbloodsample_sample', 'et_ophindnasample_sample');
    }

    public function down()
    {
        if ($this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInDnasample'))->queryRow()) {
            $group = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('name=:name', array(':name' => 'Investigation events'))->queryRow();
            $rowID = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInDnasample'))->queryRow();
            $this->update('event_type', array('class_name' => 'OphInBloodsample', 'name' => 'DNA sample'), 'id = '.$rowID['id'].' AND event_group_id = '.$group['id']);
        }

        $event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInBloodsample'))->queryRow();

        $element_type_row = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('class_name=:class_name', array(':class_name' => 'Element_OphInDnasample_Sample'))->queryRow();
        $this->update('element_type', array( 'name' => 'Sample', 'class_name' => 'Element_OphInBloodsample_Sample', 'event_type_id' => $event_type['id'], 'display_order' => 1), 'id = '.$element_type_row['id'].' AND '.$event_type['id']);


        $this->renameTable('ophindnasample_sample_type', 'ophinbloodsample_sample_type');
        $this->renameTable('et_ophindnasample_sample', 'et_ophinbloodsample_sample');
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
