<?php

class m210427_105725_create_drawing_templates_table extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable('drawing_templates', [
            'id' => 'pk',
            'name' => 'VARCHAR(50) NOT NULL',
            'event_type_id' => 'INT UNSIGNED DEFAULT NULL',
            'element_type_id' => 'INT(10) UNSIGNED DEFAULT NULL',
            'display_order' => 'TINYINT NOT NULL DEFAULT 1',
            'protected_file_id' => 'INT(10) UNSIGNED NOT NULL',
            'active' => 'BOOLEAN DEFAULT TRUE',
        ]);

        $this->addForeignKey('event_type_drawing_id_fk', 'drawing_templates', 'event_type_id', 'event_type', 'id');
        $this->addForeignKey('element_type_drawing_id_fk', 'drawing_templates', 'element_type_id', 'element_type', 'id');
        $this->addForeignKey('protected_file_drawing_id_fk', 'drawing_templates', 'protected_file_id', 'protected_file', 'id');

        $this->importTemplates();
    }

    private function importTemplates()
    {
        $path = Yii::getPathOfAlias('application') . '/migrations/data/freehand_templates/';
        echo "\n Importing freehand drawing templates from $path\n";
        $files = array_diff(scandir($path), array('.', '..'));

        $data = [];
        foreach ($files as $file_name) {
            echo "\n... $file_name\n";
            $name = ucfirst(str_replace('_', ' ', str_replace("_background", "", $file_name)));
            $file = ProtectedFile::createFromFile($path . "/$file_name");
            $file->name = $name;
            $file->title = $name;
            if ($file->save()) {
                $data[] = [
                    'name' => substr($name, 0, (strrpos($name, "."))), //remove extension from name
                    'protected_file_id' => $file->id,

                ];
            }
        }

        $this->insertMultiple('drawing_templates', $data);
    }

    public function safeDown()
    {
        $this->dropForeignKey('event_type_drawing_id_fk', 'drawing_templates');
        $this->dropForeignKey('element_type_drawing_id_fk', 'drawing_templates');
        $this->dropForeignKey('protected_file_drawing_id_fk', 'drawing_templates');

        $this->dropOETable('drawing_templates');
    }
}
