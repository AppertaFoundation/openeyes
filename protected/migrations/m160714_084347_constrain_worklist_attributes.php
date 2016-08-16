<?php

class m160714_084347_constrain_worklist_attributes extends OEMigration
{
    public function up()
    {
        $to_delete = array();
        $current_worklist_patient_id = null;
        $current_attr_ids = array();
        foreach ($this->dbConnection->createCommand()->select('*')->from('worklist_patient_attribute')->order('worklist_patient_id, created_date desc')->queryAll() as $wp_attr) {
            if ($current_worklist_patient_id != $wp_attr['worklist_patient_id']) {
                $current_worklist_patient_id = $wp_attr['worklist_patient_id'];
                $current_attr_ids = array();
            }
            if (!in_array($wp_attr['worklist_attribute_id'], $current_attr_ids)) {
                $current_attr_ids[] = $wp_attr['worklist_attribute_id'];
                continue;
            }
            $to_delete[] = $wp_attr['id'];
        }
        if (count($to_delete)) {
            $this->dbConnection->createCommand()->delete('worklist_patient_attribute', 'id in ('.implode(',', $to_delete).')');
        }

        $this->createIndex('worklist_patient_attribute_p_unique', 'worklist_patient_attribute', 'worklist_attribute_id, worklist_patient_id', true);
    }

    public function down()
    {
        $this->dropIndex('worklist_patient_attribute_p_unique', 'worklist_patient_attribute');
    }
}
