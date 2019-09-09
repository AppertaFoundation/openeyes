<?php

class m170422_124217_add_csm_shortcode extends CDbMigration
{

    private function _getEventType()
    {
        return $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphCiExamination'))->queryRow();
    }

    public function up()
    {
        $event_type = $this->_getEventType();

        $this->insert('patient_shortcode', array(
                'event_type_id' => $event_type['id'],
                'code' => 'csm',
                'default_code' => 'csm',
                'method' => 'getCataractSurgicalManagement',
                'description' => 'Cataract Surgical Management',
        ));
    }

    public function down()
    {
        $event_type = $this->_getEventType();
        $this->execute('DELETE FROM patient_shortcode
                        WHERE code = :code
                        AND event_type_id = :event_type_id',
                        array(':code'=>'csm', ':event_type_id'=>$event_type['id']));
    }
}