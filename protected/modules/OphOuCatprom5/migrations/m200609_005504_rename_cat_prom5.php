<?php

class m200609_005504_rename_cat_prom5 extends OEMigration
{
    public function safeUp()
    {
        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name'=>'OphOuCatprom5'))->queryRow();
        $this->update('event_type', ['name' => 'Cat-PROM5'], "id = :event_type", array('event_type'=> $event_type_id['id']));
    }

    public function safeDown()
    {
        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name'=>'OphOuCatprom5'))->queryRow();
        $this->update('event_type', ['name' => 'CatProm5'], "id = :event_type", array('event_type'=> $event_type_id['id']));
    }
}
