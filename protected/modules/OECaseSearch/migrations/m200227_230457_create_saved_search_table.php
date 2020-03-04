<?php

class m200227_230457_create_saved_search_table extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function up()
    {
        $this->createOETable(
            'case_search_saved_search',
            array(
                'id' => 'pk',
                'name' => 'varchar(50) NOT NULL',
                'search_criteria' => 'text NOT NULL',
            ),
            true
        );
    }

    public function down()
    {
        $this->dropOETable(
            'case_search_saved_search',
            true
        );
    }
}
