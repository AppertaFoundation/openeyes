<?php

class m170420_183805_add_ode_shortcode extends CDbMigration
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
            'code' => 'ode',
            'default_code' => 'ode',
            'method' => 'getLatestOutcomeDetails',
            'description' => 'Outcome details from latest Examination',
        ));

    }

    public function down()
    {
        $event_type = $this->_getEventType();
        $this->execute(
            'DELETE FROM patient_shortcode
                        WHERE code = :code
                        AND event_type_id = :event_type_id',
            array(':code'=>'ode', ':event_type_id'=>$event_type['id'])
        );
    }
}
