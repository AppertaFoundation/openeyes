<?php

class m131016_104922_injection_number_both_shortcode extends CDbMigration
{
    public function up()
    {
        $inj_event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')
            ->where('class_name=:cname', array(':cname' => 'OphTrIntravitrealinjection'))->queryRow();

        $inj_event_type_id = $inj_event_type['id'];

        $this->insert('patient_shortcode', array(
                'event_type_id' => $inj_event_type_id,
                'default_code' => 'inb',
                'code' => 'inb',
                'method' => 'getLetterTreatmentNumberBoth',
                'description' => 'Treatment number both eyes',
            ));
    }

    public function down()
    {
        $this->delete('patient_shortcode', 'method = :method', array(':method' => 'getLetterTreatmentNumberBoth'));
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
