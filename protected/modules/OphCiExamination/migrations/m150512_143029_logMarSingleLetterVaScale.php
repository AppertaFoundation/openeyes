<?php

class m150512_143029_logMarSingleLetterVaScale extends CDbMigration
{
    public function up()
    {
        $this->insert('ophciexamination_visual_acuity_unit', array('name' => 'logMAR single-letter', 'active' => '1'));
        $unitId = $this->dbConnection->createCommand()->select('id')->from('ophciexamination_visual_acuity_unit')->where('name = :name', array(':name' => 'logMAR single-letter'))->queryScalar();

        $datafile = fopen(dirname(__FILE__).'/data/m150512_143029_logMarSingleLetterVaScale/01_ophciexamination_visual_acuity_unit_value.csv', 'r');
        $columns = fgetcsv($datafile);
        while (($record = fgetcsv($datafile)) !== false) {
            $this->insert('ophciexamination_visual_acuity_unit_value', array('unit_id' => $unitId, $columns[0] => $record[0], $columns[1] => $record[1], 'selectable' => '1'));
        }
    }

    public function down()
    {
        $unitId = $this->dbConnection->createCommand()->select('id')->from('ophciexamination_visual_acuity_unit')->where('name = :name', array(':name' => 'logMAR single-letter'))->queryScalar();

        $this->delete('ophciexamination_visual_acuity_unit_value', 'unit_id = :unit_id', array(':unit_id' => $unitId));
        $this->delete('ophciexamination_visual_acuity_unit', 'id = :id', array(':id' => $unitId));

        return true;
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
