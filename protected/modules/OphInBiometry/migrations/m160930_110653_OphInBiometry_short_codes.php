<?php

class m160930_110653_OphInBiometry_short_codes extends CDbMigration
{
    public function up()
    {
        $eventTypeId = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name=:class_name', array(':class_name' => 'OphInBiometry'))
            ->queryScalar();

        $this->insert('patient_shortcode', array(
            'event_type_id' => $eventTypeId,
            'default_code' => 'rxt',
            'code' => 'rxt',
            'method' => "getLastBiometryTargetRefraction",
            'description' => "Last Biometry target refraction",
        ));

    }

    public function down()
    {
        $this->delete('patient_shortcode', 'default_code = :code', array(':code' => 'rxt'));
    }


}
