<?php

class m200715_122047_update_old_element_set_item_to_use_new_element extends CDbMigration
{
    public function safeUp()
    {
        $archive_cataract_surgical_management_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_type')
            ->where('class_name = :class_name',
                [':class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_CataractSurgicalManagement_Archive'])
            ->queryScalar();

        $new_cataract_surgical_management_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_type')
            ->where('class_name = :class_name',
                [':class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_CataractSurgicalManagement'])
            ->queryScalar();

        $this->update('ophciexamination_element_set_item',
            ['element_type_id' => $new_cataract_surgical_management_id],
            'element_type_id = :old_element_type_id',
            [':old_element_type_id' => $archive_cataract_surgical_management_id]
        );
    }

    public function safeDown()
    {
        echo "m200715_122047_update_old_element_set_item_to_use_new_elementdoes not support migration down.\n";
    }
}
