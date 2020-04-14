<?php

class m160930_071702_OphTrOperationnote_short_codes extends CDbMigration
{
    private $short_codes_for_op_note = array(
        array('code' => 'lod', 'method' => 'getLastOperationDate', 'description' => 'Last Operation Date'),
        array(
            'code' => 'los',
            'method' => 'getLastOperationSurgeonName',
            'description' => 'Last Operation Surgeon Name',
        ),
        array('code' => 'lol', 'method' => 'getLastOperationLocation', 'description' => 'Last Operation Location'),
        array(
            'code' => 'opm',
            'method' => 'getLastOperationIncisionMeridian',
            'description' => 'Last Operation Incision Meridian',
        ),
        array(
            'code' => 'rxp',
            'method' => 'getLastOperationPredictedRefraction',
            'description' => 'Last Operation Predicted Refraction',
        ),
        array('code' => 'opv', 'method' => 'getLastOperationDetails', 'description' => 'Last operation details'),
    );

    public function up()
    {

        $eventTypeId = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name=:class_name', array(':class_name' => 'OphTrOperationnote'))
            ->queryScalar();
        foreach ($this->short_codes_for_op_note as $short_code) {
            $this->insert('patient_shortcode', array(
                'event_type_id' => $eventTypeId,
                'default_code' => $short_code['code'],
                'code' => $short_code['code'],
                'method' => $short_code['method'],
                'description' => $short_code['description'],
            ));
        }
    }

    public function down()
    {
        foreach ($this->short_codes_for_op_note as $short_code) {
            $this->delete('patient_shortcode', 'default_code = :code', array(':code' => $short_code['code']));
        }
    }

}
