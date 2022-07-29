<?php

class m220315_030533_create_indices_of_deprivation_tables extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->createTable(
            'postcode_to_lsoa_mapping',
            array(
                'id' => 'pk',
                'postcode'=>'varchar(7)',
                'lsoa' => 'varchar(9)',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\''
            )
        );

        $this->createTable(
            'lsoa_to_imd_mapping',
            array(
                'id' => 'pk',
                'lsoa' => 'varchar(9)',
                'imd_score' => 'float',
                'imd_rank' => 'int(32)',
                'imd_decile' => 'int(4)',
                'imd_import_id' => 'int(11)',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\''
            )
        );

        $this->createTable(
            'imd_import',
            array(
                'id' => 'pk',
                'country' => 'varchar(50)',
                'file_name' => 'varchar(255)',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\''
            )
        );

        $this->addForeignKey(
            'imd_import_fk',
            'lsoa_to_imd_mapping',
            'imd_import_id',
            'imd_import',
            'id'
        );

        $this->execute('ALTER TABLE lsoa_to_imd_mapping ADD UNIQUE INDEX lsoa_to_imd_mapping_lsoa_idx (lsoa) USING HASH;');
        $this->execute('ALTER TABLE postcode_to_lsoa_mapping ADD INDEX postcode_to_lsoa_mapping_lsoa_idx (lsoa) USING HASH;');
        $this->execute('ALTER TABLE postcode_to_lsoa_mapping ADD UNIQUE INDEX postcode_to_lsoa_mapping_postcode_idx (postcode) USING HASH;');
        $this->execute('ALTER TABLE `address` ADD INDEX address_postcode_idx (postcode) USING HASH;');
    }

    public function safeDown()
    {
        $this->dropForeignKey('imd_to_import_fk', 'lsoa_to_imd_mapping');

        $this->dropTable('imd_import');
        $this->dropTable('lsoa_to_imd_mapping');
        $this->dropTable('postcode_to_lsoa_mapping');
    }
}
