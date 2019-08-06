<?php

class m170419_104800_add_more_snellen_VA_values extends OEMigration
{
    public function up()
    {
        $unit = $this->dbConnection->createCommand()->select('id')->from('ophciexamination_visual_acuity_unit')->where('name = :name', array(':name' => 'Snellen Metre'))->queryRow();

        # Check that these values do not already exist
        $s675 = $this->dbConnection->createCommand()->select('id')->from('ophciexamination_visual_acuity_unit_value')->where('base_value = :bval AND unit_id = :unit', array(':bval' => '105', ':unit' => $unit['id']))->queryRow();
        $s695 = $this->dbConnection->createCommand()->select('id')->from('ophciexamination_visual_acuity_unit_value')->where('base_value = :bval AND unit_id = :unit', array(':bval' => '100', ':unit' => $unit['id']))->queryRow();

        # Insert values if they don't already exist
        if ($s675['id'] == '') {
            $this->insert('ophciexamination_visual_acuity_unit_value', array(
                        'unit_id' => $unit['id'],
                        'value' => '6/7.5',
                        'base_value' => '105',
                ));
        }

        if ($s695['id'] == '') {
            $this->insert('ophciexamination_visual_acuity_unit_value', array(
                        'unit_id' => $unit['id'],
                        'value' => '6/9.5',
                        'base_value' => '100',
                ));
        }
    }

    public function down()
    {
        $unit = $this->dbConnection->createCommand()->select('id')->from('ophciexamination_visual_acuity_unit')->where('name = :name', array(':name' => 'Snellen Metre'))->queryRow();

        $this->delete('ophciexamination_visual_acuity_unit_value', 'value = :value AND unit_id = :unit_id', array(':value' => '6/7.5', ':unit_id' => $unit['id']));
        $this->delete('ophciexamination_visual_acuity_unit_value', 'value = :value AND unit_id = :unit_id', array(':value' => '6/9.5', ':unit_id' => $unit['id']));
    }
}
