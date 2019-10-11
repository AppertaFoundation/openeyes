<?php

class m190701_012231_add_fp10_wp10_dispense_condition extends OEMigration
{
    private $condition; // This is set at the start of each operation because it relies on a Yii config parameter.
    private $location = 'N/A';

    public function safeUp()
    {
        $this->condition = 'Print to {form_type}';
        $this->insert('ophdrprescription_dispense_condition', array('name' => $this->condition));

        $condition_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('ophdrprescription_dispense_condition')
            ->where("name='".$this->condition."'")->queryScalar();
        if ($condition_id) {
            $location_id = $this->dbConnection->createCommand()
                ->select('id')
                ->from('ophdrprescription_dispense_location')
                ->where("name='".$this->location."'")->queryScalar();
            if ($location_id) {
                $this->insert('ophdrprescription_dispense_condition_assignment',
                    array(
                        'dispense_condition_id' => $condition_id,
                        'dispense_location_id' => $location_id
                    )
                );
            }
        }
    }

    public function safeDown()
    {
        $this->condition = 'Print to {form_type}';
        $condition_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('ophdrprescription_dispense_condition')
            ->where("name = '".$this->condition."'")->queryScalar();

        if ($condition_id) {
            $this->delete('ophdrprescription_dispense_condition_assignment',
                'dispense_condition_id=?',
                array($condition_id)
            );
        }

        $this->delete('ophdrprescription_dispense_condition', 'name=?', array($this->condition));


    }
}