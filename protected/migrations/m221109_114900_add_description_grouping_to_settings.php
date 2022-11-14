<?php

class m221109_114900_add_description_grouping_to_settings extends OEMigration
{
    public function safeUp()
    {
        // Add new columns
        if (!$this->verifyColumnExists('setting_metadata', 'group_id')) {
            $this->addOEColumn('setting_metadata', 'group_id', 'INT AFTER `name`', true);
        }
        if (!$this->verifyColumnExists('setting_metadata', 'description')) {
            $this->addOEColumn('setting_metadata', 'description', 'TEXT AFTER `name`', true);
        }

        // Extend the name column
        $this->alterOEColumn('setting_metadata', 'name', 'VARCHAR(100)', true);

        // create group table
        if (!$this->verifyTableExists('setting_group')) {
            $this->createOETable('setting_group', [
                'id int NOT NULL AUTO_INCREMENT',
                'name VARCHAR(40) UNIQUE',
                'PRIMARY KEY (id)'
            ], false);

            $this->addForeignKey('fk_setting_group', 'setting_metadata', 'group_id', 'setting_group', 'id');
        }

        $filename = Yii::app()->basePath . "/migrations/data/m221109_114900_add_description_grouping_to_settings/setting_metadata_202208211803.xlsx";
        echo $filename;
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);

        foreach ($spreadsheet->getSheet(0)->toArray() as $index => $data) {
            //skip first row as it is column headers
            if ($index == 0) {
                continue;
            }
            // Add the group
            $this->execute("INSERT IGNORE INTO setting_group (`name`) VALUES (:group)", [':group' => $data[3]]);

            $this->execute(
                "UPDATE setting_metadata
                            SET `description` = :description,
                                group_id = (SELECT id FROM setting_group WHERE `name` = :group),
                                `name` = :name
                            WHERE `key` = :key AND (element_type_id = :element_id OR element_type_id IS NULL)",
                [
                                ':key' => $data[0],
                                ':element_id' => $data[1],
                                ':description' => $data[4],
                                ':group' => $data[3],
                                ':name' => $data[2]
                ]
            );
        }

        // Ensure that all future settings must have a group and description assigned
        $this->alterOEColumn('setting_metadata', 'description', 'TEXT NOT NULL', true);
        $this->alterOEColumn('setting_metadata', 'group_id', 'INT NOT NULL', true);
    }

    public function safeDown()
    {
        $this->dropOEColumn('setting_metadata', 'group');
        $this->dropOEColumn('setting_metadata', 'description');
        $this->alterOEColumn('setting_metadata', 'name', 'VARCHAR(64)', true);
    }
}
