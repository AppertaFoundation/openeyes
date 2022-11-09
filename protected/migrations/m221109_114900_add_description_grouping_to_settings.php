<?php

class m221109_114900_add_description_grouping_to_settings extends OEMigration
{
    public function safeUp()
    {
        // Add new columns
        if (!$this->verifyColumnExists('setting_metadata', 'group')) {
            $this->addOEColumn('setting_metadata', 'group', 'VARCHAR(40) AFTER `name`', true);
        }
        if (!$this->verifyColumnExists('setting_metadata', 'description')) {
            $this->addOEColumn('setting_metadata', 'description', 'VARCHAR(1000) AFTER `name`', true);
        }

        // CSV File with following order- key, element_type_id, name, group, description
        $file = fopen(Yii::app()->basePath . "/migrations/data/m221109_114900_add_description_grouping_to_settings/setting_metadata_202208211803.csv", "r");
        while (($data = fgetcsv($file, 1000, ',')) !== false) {
            $this->update(
                'setting_metadata',
                [
                    'description' => $data[4],
                    'group' => $data[3],
                    'name' => $data[2]
                ],
                "`key` = :key AND (element_type_id = :element_id OR element_type_id IS NULL)",
                [
                    ':key' => $data[0],
                    ':element_id' => $data[1]
                ]
            );
        }
    }

    public function safeDown()
    {
        $this->dropOEColumn('setting_metadata', 'group');
        $this->dropOEColumn('setting_metadata', 'description');
    }
}
