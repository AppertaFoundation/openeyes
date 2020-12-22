<?php

class m200424_155043_sort_workflow_element_set_order extends CDbMigration
{
    public function safeUp()
    {
        $processed_workflow_id = 1;
        $position = 0;
        $criteria = new CDbCriteria();
        $criteria->order = 'workflow_id asc, position asc';
        $data_provider = new CActiveDataProvider('OEModule\OphCiExamination\models\OphCiExamination_ElementSet', ['criteria' => $criteria]);
        $element_set_iterator = new CDataProviderIterator($data_provider);

        foreach ($element_set_iterator as $element_set) {
            if ($element_set->workflow_id === $processed_workflow_id) {
                 ++$position;
            } else {
                $position = 1;
            }
            $element_set->saveCounters(array('position' => -$element_set->position)); //set position in db to 0
            $element_set->saveCounters(array('position' => $position));
            $processed_workflow_id = $element_set->workflow_id;
        }
    }

    public function safeDown()
    {
        echo "m200424_155043_sort_workflow_element_set_order does not support migration down.\n";
    }
}
