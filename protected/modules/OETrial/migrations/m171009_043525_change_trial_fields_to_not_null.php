<?php

class m171009_043525_change_trial_fields_to_not_null extends OEMigration
{
    public function changeColumnToNullable($tableName, $columnName)
    {
        $columnType = $this->getColumnType($tableName, $columnName);
        $this->execute("ALTER TABLE $tableName MODIFY $columnName $columnType NULL");
    }

    public function changeColumnToNotNullable($tableName, $columnName)
    {
        $columnType = $this->getColumnType($tableName, $columnName);
        $this->execute("ALTER TABLE $tableName MODIFY $columnName $columnType NOT NULL");
    }

    public function getColumnType($tableName, $columnName)
    {
       $command = Yii::app()->db->createCommand(<<<EOSQL
SELECT CONCAT(data_type, '(', character_maximum_length, ')')
FROM information_schema.columns
WHERE table_name = '$tableName' AND column_name = '$columnName'
AND table_schema = DATABASE()
EOSQL
);
       return $command->queryScalar();
    }
    
    public function up()
    {
        $this->changeColumnToNotNullable('trial', 'trial_type');
        $this->changeColumnToNotNullable('trial_version', 'trial_type');

        $this->changeColumnToNotNullable('trial_patient', 'patient_status');
        $this->changeColumnToNotNullable('trial_patient_version', 'patient_status');

        $this->changeColumnToNotNullable('trial_patient', 'treatment_type');
        $this->changeColumnToNotNullable('trial_patient_version', 'treatment_type');

        $this->changeColumnToNotNullable('user_trial_permission', 'permission');
        $this->changeColumnToNotNullable('user_trial_permission_version', 'permission');
    }

    public function down()
    {

        $this->changeColumnToNullable('trial', 'trial_type');
        $this->changeColumnToNullable('trial_version', 'trial_type');

        $this->changeColumnToNullable('trial_patient', 'patient_status');
        $this->changeColumnToNullable('trial_patient_version', 'patient_status');

        $this->changeColumnToNullable('trial_patient', 'treatment_type');
        $this->changeColumnToNullable('trial_patient_version', 'treatment_type');

        $this->changeColumnToNullable('user_trial_permission', 'permission');
        $this->changeColumnToNullable('user_trial_permission_version', 'permission');
    }
}