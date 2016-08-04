<?php

class m160421_131246_add_indexes_to_HSCIC_tables extends CDbMigration
{
    public function up()
    {
        $this->createIndex('commissioning_body_type_shortname', 'commissioning_body_type', 'shortname');
        $this->createIndex('gp_nat_id', 'gp', 'nat_id');
        $this->createIndex('practice_code', 'practice', 'code');
        $this->createIndex('commissioning_body_code', 'commissioning_body', 'code');
    }

    public function down()
    {
        $this->dropIndex('commissioning_body_type_shortname', 'commissioning_body_type');
        $this->dropIndex('gp_nat_id', 'gp');
        $this->dropIndex('practice_code', 'practice');
        $this->dropIndex('commissioning_body_code', 'commissioning_body');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
