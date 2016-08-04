<?php

class m141202_134300_add_admission_letter_warnings extends \CDbMigration
{
    public function up()
    {
        $types = array(
            'Child health advice',
            'Preop Assessment',
            'Prescription',
            'Admission Instruction',
            'Seating',
            'Prescription charges',
        );
        foreach ($types as $type) {
            $command = $this->dbConnection->createCommand()
                ->select('id')
                ->from('ophtroperationbooking_admission_letter_warning_rule_type')
                ->where('name = :name');
            if (!$command->queryScalar(array(':name' => $type))) {
                $this->insert('ophtroperationbooking_admission_letter_warning_rule_type', array('name' => $type));
            }
        }
    }

    public function down()
    {
    }
}
