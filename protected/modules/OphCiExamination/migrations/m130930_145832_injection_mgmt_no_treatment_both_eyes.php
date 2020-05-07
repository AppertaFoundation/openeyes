<?php

class m130930_145832_injection_mgmt_no_treatment_both_eyes extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophciexamination_injectionmanagementcomplex', 'left_no_treatment', 'boolean');
        $this->addColumn('et_ophciexamination_injectionmanagementcomplex', 'right_no_treatment', 'boolean');
        $this->addColumn('et_ophciexamination_injectionmanagementcomplex', 'left_no_treatment_reason_id', 'int(10) unsigned');
        $this->addColumn('et_ophciexamination_injectionmanagementcomplex', 'right_no_treatment_reason_id', 'int(10) unsigned');
        $this->addColumn('et_ophciexamination_injectionmanagementcomplex', 'left_no_treatment_reason_other', 'text');
        $this->addColumn('et_ophciexamination_injectionmanagementcomplex', 'right_no_treatment_reason_other', 'text');

        foreach ($this->dbConnection->createCommand()->select('id, no_treatment_reason_id, no_treatment_reason_other')
                     ->from('et_ophciexamination_injectionmanagementcomplex')
                     ->where('no_treatment = true')
                     ->order('id desc')->queryAll() as $imc) {
            $this->update(
                'et_ophciexamination_injectionmanagementcomplex',
                array(
                    'left_no_treatment' => true,
                    'right_no_treatment' => true,
                    'left_no_treatment_reason_id' => $imc['no_treatment_reason_id'],
                    'right_no_treatment_reason_id' => $imc['no_treatment_reason_id'],
                    'left_no_treatment_reason_other' => $imc['no_treatment_reason_other'],
                    'right_no_treatment_reason_other' => $imc['no_treatment_reason_other'], ),
                'id = :id',
                array(':id' => $imc['id'])
            );
        }

        $this->dropColumn('et_ophciexamination_injectionmanagementcomplex', 'no_treatment');
        $this->dropColumn('et_ophciexamination_injectionmanagementcomplex', 'no_treatment_reason_id');
        $this->dropColumn('et_ophciexamination_injectionmanagementcomplex', 'no_treatment_reason_other');
    }

    public function down()
    {
        $this->addColumn('et_ophciexamination_injectionmanagementcomplex', 'no_treatment_reason_other', 'text');
        $this->addColumn('et_ophciexamination_injectionmanagementcomplex', 'no_treatment_reason_id', 'int(10) unsigned');
        $this->addColumn('et_ophciexamination_injectionmanagementcomplex', 'no_treatment', 'boolean');

        foreach ($this->dbConnection->createCommand()
                     ->select('id, left_no_treatment, right_no_treatment, left_no_treatment_reason_id, right_no_treatment_reason_id, left_no_treatment_reason_other, right_no_treatment_reason_other')
                     ->from('et_ophciexamination_injectionmanagementcomplex')
                     ->where('left_no_treatment = true or right_no_treatment = true')
                     ->order('id desc')->queryAll() as $imc) {
            // favour the left eye by default - this is not a completely clean downward migration and is only really for use
            // before it has been used interactively.
            $side = $imc['left_no_treatment'] ? 'left' : 'right';

            $this->update(
                'et_ophciexamination_injectionmanagementcomplex',
                array(
                    'no_treatment' => true,
                    'no_treatment_reason_id' => $imc[$side.'_no_treatment_reason_id'],
                    'no_treatment_reason_other' => $imc[$side.'_no_treatment_reason_other'], ),
                'id = :id',
                array(':id' => $imc['id'])
            );
        }

        $this->dropColumn('et_ophciexamination_injectionmanagementcomplex', 'left_no_treatment');
        $this->dropColumn('et_ophciexamination_injectionmanagementcomplex', 'right_no_treatment');
        $this->dropColumn('et_ophciexamination_injectionmanagementcomplex', 'left_no_treatment_reason_id');
        $this->dropColumn('et_ophciexamination_injectionmanagementcomplex', 'right_no_treatment_reason_id');
        $this->dropColumn('et_ophciexamination_injectionmanagementcomplex', 'left_no_treatment_reason_other');
        $this->dropColumn('et_ophciexamination_injectionmanagementcomplex', 'right_no_treatment_reason_other');

        //echo "m130930_145832_injection_mgmt_no_treatment_both_eyes does not support migration down.\n";
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
