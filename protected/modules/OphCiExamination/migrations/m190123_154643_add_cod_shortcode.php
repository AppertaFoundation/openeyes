<?php

class m190123_154643_add_cod_shortcode extends CDbMigration
{
    public function up()
    {
        $event_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name = :class_name', [':class_name' => 'OphCiExamination'])
            ->queryScalar();

        $this->insert('patient_shortcode', [
            'event_type_id' => $event_type_id,
            'code' => 'cod',
            'default_code' => 'cod',
            'method' => 'getCurrentOphthalmicDrugs',
            'description' => 'Current Ophthalmic Drugs',
        ]);
    }

    public function down()
    {
        $this->delete('patient_shortcode', 'code = ?', ['cod']);
    }
}
