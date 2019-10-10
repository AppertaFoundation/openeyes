<?php

class m170307_115007_add_poi_shortcode extends CDbMigration
{
    private $shortCodeForPoi = array(
        array('code' => 'poi', 'method' => 'getLastOperationPostOpInstructions', 'description' => 'Last operation post op instructions'),
    );

    public function up()
    {
        $eventTypeId = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name=:class_name', array(':class_name' => 'OphTrOperationnote'))
            ->queryScalar();
        foreach ($this->shortCodeForPoi as $short_code) {
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
        foreach ($this->shortCodeForPoi as $short_code) {
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