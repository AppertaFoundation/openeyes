<?php

class m200424_155043_sort_workflow_element_set_order extends CDbMigration
{
    public function safeUp()
    {
        $processed_workflow_id = 1;
        $position = 0;
        $criteria = new CDbCriteria();
        $criteria->order = ' workflow_id asc, position asc';
        $dataProvider = new CActiveDataProvider('OEModule\OphCiExamination\models\OphCiExamination_ElementSet', ['criteria' => $criteria]);
        $element_set_iterator = new CDataProviderIterator($dataProvider);

        foreach ($element_set_iterator as $element_set) {
            if ($element_set->workflow_id === $processed_workflow_id) {
                $element_set->position = ++$position;
                $processed_workflow_id = $element_set->workflow_id;
            } else {
                $element_set->position = $position = 1;
            }
            $element_set->save();
            $processed_workflow_id = $element_set->workflow_id;
        }
    }

    public function safeDown()
    {
        echo "m200424_155043_sort_workflow_element_set_order does not support migration down.\n";
    }
}