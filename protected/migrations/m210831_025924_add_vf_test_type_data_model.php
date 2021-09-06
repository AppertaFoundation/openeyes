<?php

class m210831_025924_add_vf_test_type_data_model extends OEMTMigration
{
    protected function getLevelStructuredTables(): array
    {
        return array(
            'user' => array(),
            'firm' => array(),
            'site' => array(),
            'subspecialty' => array(),
            'specialty' => array(),
            'institution' => array(
                'visual_field_test_preset',
            ),
        );
    }

    public function safeUp()
    {
        $this->createOETable(
            'visual_field_test_type',
            array(
                'id' => 'pk',
                'long_name' => 'varchar(100)',
                'short_name' => 'varchar(20)',
                'active' => 'tinyint(1) unsigned DEFAULT 1',
            ),
            true
        );
        $this->createOETable(
            'visual_field_test_option',
            array(
                'id' => 'pk',
                'long_name' => 'varchar(100)',
                'short_name' => 'varchar(20)',
                'active' => 'tinyint(1) unsigned DEFAULT 1',
            ),
            true
        );
        $this->createOETable(
            'visual_field_test_preset',
            array(
                'id' => 'pk',
                'test_type_id' => 'int NOT NULL',
                'option_id' => 'int',
                'name' => 'varchar(100)',
            ),
            true
        );

        $this->addForeignKey(
            'visual_field_test_preset_type_fk',
            'visual_field_test_preset',
            'test_type_id',
            'visual_field_test_type',
            'id'
        );

        $this->addForeignKey(
            'visual_field_test_preset_option_fk',
            'visual_field_test_preset',
            'option_id',
            'visual_field_test_option',
            'id'
        );

        $this->insertMultiple(
            'visual_field_test_type',
            [
                ['long_name' => '10-2', 'short_name' => '10-2'],
                ['long_name' => '24-2', 'short_name' => '24-2'],
                ['long_name' => '30-2', 'short_name' => '30-2'],
                ['long_name' => 'Estermann', 'short_name' => 'Estermann'],
                ['long_name' => 'Goldmann', 'short_name' => 'Goldmann'],
            ]
        );
        $this->insertMultiple(
            'visual_field_test_option',
            [
                ['long_name' => 'Standard', 'short_name' => 'Standard'],
                ['long_name' => 'Fast', 'short_name' => 'Fast'],
                ['long_name' => 'Faster', 'short_name' => 'Faster'],
            ]
        );
        return parent::safeUp();
    }

    public function safeDown()
    {
        parent::safeDown();
        $this->dropOETable('visual_field_test_preset', true);
        $this->dropOETable('visual_field_test_option', true);
        $this->dropOETable('visual_field_test_type', true);
    }
}
