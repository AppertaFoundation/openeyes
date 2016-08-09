<?php

class m131010_130039_injections_both_shortcode extends CDbMigration
{
    public function up()
    {
        $inj_event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')
            ->where('class_name=:cname', array(':cname' => 'OphTrIntravitrealinjection'))->queryRow();

        $inj_event_type_id = $inj_event_type['id'];

        $this->insert('patient_shortcode', array(
                'event_type_id' => $inj_event_type_id,
                'default_code' => 'idb',
                'code' => 'idb',
                'method' => 'getLetterTreatmentDrugBoth',
                'description' => 'Treatment drug both eyes',
            ));
    }

    public function down()
    {
        $this->delete('patient_shortcode', 'method = :method', array(':method' => 'getLetterTreatmentDrugBoth'));
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
