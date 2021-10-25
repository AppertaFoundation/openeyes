<?php

class m210401_010514_add_new_print_prescription_role extends CDbMigration
{
    const PRINT_PRESCRIPTION_ROLE = 'Print Prescription';
    const PRINT_PRESCRIPTION_TASK = 'TaskPrintPrescription';
    const PRINT_PRESCRIPTION_OPERATION = 'OprnPrintPrescription';

    public function safeUp()
    {
        $this->insert('authitem', array('name' => self::PRINT_PRESCRIPTION_TASK, 'type' => 1));
        $this->insert('authitem', array('name' => self::PRINT_PRESCRIPTION_ROLE, 'type' => 2));
        $this->insert(
            'authitemchild',
            array('parent' => self::PRINT_PRESCRIPTION_ROLE, 'child' => self::PRINT_PRESCRIPTION_TASK)
        );
        $this->insert(
            'authitemchild',
            array('parent' => self::PRINT_PRESCRIPTION_TASK, 'child' => self::PRINT_PRESCRIPTION_OPERATION)
        );

        $this->delete(
            'authitemchild',
            'parent = "TaskPrescribe" AND child = "OprnPrintPrescription"'
        );

        $this->delete(
            'authitemchild',
            'parent = "TaskPrint" AND child = "OprnPrintPrescription"'
        );

        // All users that currently have both the prescribe and print roles should be given the Print prescription role.
        $userIds = $this->dbConnection->createCommand("select userid, 'Print Prescription' AS 'itemname' from authassignment where itemname IN ('Print', 'Prescribe') group by userid having count(*) = 2;")->queryAll();

        if(!empty($userIds)){
            $this->insertMultiple('authassignment', $userIds);
        }
    }

    public function safeDown()
    {
        $this->delete(
            'authitemchild',
            'parent = "' . self::PRINT_PRESCRIPTION_TASK . '" AND child = "' . self::PRINT_PRESCRIPTION_OPERATION . '"'
        );

        $this->delete(
            'authitemchild',
            'parent = "' . self::PRINT_PRESCRIPTION_ROLE . '" AND child = "' . self::PRINT_PRESCRIPTION_TASK . '"'
        );

        $this->delete('authitem', 'name = "' . self::PRINT_PRESCRIPTION_ROLE . '"');
        $this->delete('authitem', 'name = "' . self::PRINT_PRESCRIPTION_TASK . '"');
    }
}
