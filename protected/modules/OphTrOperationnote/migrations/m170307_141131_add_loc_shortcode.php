<?php

class m170307_141131_add_loc_shortcode extends CDbMigration
{
    private $shortPeriOperativeComp = array(
        array('code' => 'loc', 'method' => 'getLastOperationPeriOperativeComplications', 'description' => 'Last operation peri-operative complications'),

    );

    public function up()
    {
        $eventTypeId = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name=:class_name', array(':class_name' => 'OphTrOperationnote'))
            ->queryScalar();
        foreach ($this->shortPeriOperativeComp as $short_code) {
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
        foreach ($this->shortPeriOperativeComp as $short_code) {
            $this->delete('patient_shortcode', 'default_code = :code', array(':code' => $short_code['code']));
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
