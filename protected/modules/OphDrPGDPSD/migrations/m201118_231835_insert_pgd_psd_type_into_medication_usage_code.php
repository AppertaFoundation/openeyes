<?php

class m201118_231835_insert_pgd_psd_type_into_medication_usage_code extends OEMigration
{
    public function safeUp()
    {
        $this->insert(
            'medication_usage_code',
            array(
                'usage_code' => 'COMMON_EYE_DROPS',
                'name' => 'Common Eye Drops',
                'active' => 1
            )
        );
        $this->insert(
            'medication_usage_code',
            array(
                'usage_code' => 'COMMON_ORAL_MEDS',
                'name' => 'Common Oral Meds',
                'active' => 1
            )
        );
    }

    public function safeDown()
    {
        $id_query_res = $this->dbConnection->createCommand()
            ->select('id')
            ->from('medication_usage_code')
            ->where('usage_code = "COMMON_EYE_DROPS" or usage_code = "COMMON_ORAL_MEDS"')
            ->queryColumn();
        if ($id_query_res) {
            foreach ($id_query_res as $id) {
                $this->delete('medication_set_rule', 'usage_code_id = :usage_code_id', array(':usage_code_id' => $id));
                $this->delete('medication_usage_code', 'id = :id', array(':id' => $id));
            }
        }
    }
}
