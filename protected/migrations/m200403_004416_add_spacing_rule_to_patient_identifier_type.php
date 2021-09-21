<?php

class m200403_004416_add_spacing_rule_to_patient_identifier_type extends OEMigration
{
    public function safeUp()
    {
        $spacing_rule = 'xxx xxx xxxx';
        if (Yii::app()->params['default_country'] === 'Australia') {
            $spacing_rule = 'xxxx xxxxx x x';
        }

        // use global institution id from settings to query the type
        $institution_global_id = null;
        $global_institution_remote_id = $this->dbConnection->createCommand()->select('value')->from('setting_installation')
                ->where('`key` = "global_institution_remote_id"')
                ->queryScalar();

        $institutions = $this->dbConnection->createCommand()
            ->select('id')
            ->from('institution')
            ->where('remote_id = :remote_id')
            ->bindValues(array(':remote_id' => $global_institution_remote_id))
            ->queryColumn();
        $count = count($institutions);
        if ($count === 1) {
            $institution_global_id = $institutions[0];
        }

        if ($institution_global_id) {
            $global_type_id = $this->dbConnection->createCommand()->select()->from('patient_identifier_type')
                ->where('usage_type = "GLOBAL" and institution_id = ?')
                ->queryScalar(array($institution_global_id));

            $this->addOEColumn('patient_identifier_type', 'spacing_rule', 'VARCHAR(255)', true);
            $this->update('patient_identifier_type', ['spacing_rule' => $spacing_rule], 'id = ' . $global_type_id);
        }
    }

    public function safeDown()
    {
        $this->dropOEColumn('patient_identifier_type', 'spacing_rule', true);
    }
}
