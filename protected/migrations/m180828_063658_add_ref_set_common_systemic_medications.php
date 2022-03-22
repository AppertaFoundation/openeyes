<?php

class m180828_063658_add_ref_set_common_systemic_medications extends CDbMigration
{
    public function safeUp()
    {
        $common_sys_id = $this->dbConnection->createCommand()->select('id')->from('medication_usage_code')->where('usage_code = :usage_code', [':usage_code' => 'COMMON_SYSTEMIC'])->queryScalar();

        $this->execute("INSERT INTO medication_set (`name`) VALUES ('Common systemic medications')");
        $ref_auto_set_id = $this->getDbConnection()->getLastInsertID();
        $this->execute("INSERT INTO medication_set_rule (medication_set_id, usage_code_id) VALUES ($ref_auto_set_id, $common_sys_id)");

        $common_meds = $this->dbConnection->createCommand('SELECT medication_id FROM medication_common')
            ->queryAll();
        foreach ($common_meds as $med) {
            $this->execute("INSERT INTO medication_set_item (medication_set_id, medication_id)
                SELECT $ref_auto_set_id, m.id FROM medication m INNER JOIN medication_common mc WHERE m.id = mc.medication_id");
        }
    }

    public function safeDown()
    {
        $common_sys_id = $this->dbConnection->createCommand()->select('id')->from('medication_usage_code')->where('usage_code = :usage_code', [':usage_code' => 'COMMON_SYSTEMIC'])->queryScalar();
        $this->execute("DELETE FROM medication_set_rule WHERE usage_code_id = $common_sys_id");
        $this->execute("DELETE FROM medication_set_item WHERE medication_set_id IN (SELECT id FROM medication_set WHERE `name` = 'Common systemic medications')");
        $this->execute("DELETE FROM medication_set WHERE `name` = 'Common systemic medications'");
    }
}
