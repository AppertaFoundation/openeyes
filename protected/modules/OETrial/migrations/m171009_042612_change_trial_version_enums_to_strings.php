<?php

class m171009_042612_change_trial_version_enums_to_strings extends CDbMigration
{
    private static $trialTypeMapping = array(
        1 => 'NON_INTERVENTION',
        2 => 'INTERVENTION',
    );

    private static $trialPatientStatusMapping = array(
        1 => 'SHORTLISTED',
        2 => 'ACCEPTED',
        3 => 'REJECTED',
    );

    private static $trialPatientTreatmentTypeMapping = array(
        1 => 'UNKNOWN',
        2 => 'INTERVENTION',
        3 => 'PLACEBO',
    );


    private static $userTrialPermissionMapping = array(
        1 => 'VIEW',
        2 => 'EDIT',
        3 => 'MANAGE',
    );

    public function changeColumnToVarchar($table, $column, $mapping)
    {
        $this->alterColumn($table, $column, 'varchar(20)');

        foreach ($mapping as $old => $new) {
            $sql = "UPDATE $table SET $column = '$new' WHERE $column = '$old'";
            $this->execute($sql);
        }
    }

    public function changeColumnToNumeric($table, $column, $oldType, $mapping)
    {
        $this->addColumn($table, $column . '_x', $oldType);

        foreach ($mapping as $old => $new) {
            $sql = "UPDATE $table SET {$column}_x = $old WHERE $column = '$new'";
            $this->execute($sql);
        }

        $this->dropColumn($table, $column);
        $this->renameColumn($table, $column . '_x', $column);
    }

    public function up()
    {
        $this->changeColumnToVarchar('trial_version', 'trial_type', self::$trialTypeMapping);
        $this->changeColumnToVarchar('trial_patient_version', 'patient_status', self::$trialPatientStatusMapping);
        $this->changeColumnToVarchar('trial_patient_version', 'treatment_type', self::$trialPatientTreatmentTypeMapping);
        $this->changeColumnToVarchar('user_trial_permission_version', 'permission', self::$userTrialPermissionMapping);
    }

    public function down()
    {
        $this->changeColumnToNumeric('trial_version', 'trial_type', 'int(11) unsigned', self::$trialTypeMapping);
        $this->changeColumnToNumeric('trial_patient_version', 'patient_status', 'int(10) unsigned',
            self::$trialPatientStatusMapping);
        $this->changeColumnToNumeric('trial_patient_version', 'treatment_type', 'int(10) unsigned',
            self::$trialPatientTreatmentTypeMapping);
        $this->changeColumnToNumeric('user_trial_permission_version', 'permission', 'int(10) unsigned',
            self::$userTrialPermissionMapping);
    }
}