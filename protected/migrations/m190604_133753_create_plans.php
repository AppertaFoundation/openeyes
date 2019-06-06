<?php

class m190604_133753_create_plans extends OEMigration
{
    public function up()
    {
        $this->createOETable('plans_problems',
            [
                'id' => 'pk',
                'name' => 'varchar(255) NOT NULL',
                'patient_id' => 'int(10) unsigned NOT NULL',
                'display_order' => 'int(10) unsigned NOT NULL',
                'active' => 'bool default true'
            ]
        , true);

        $this->addForeignKey('plans_problems_user_fk',
            'plans_problems', 'patient_id',
            'patient', 'id');
    }

    public function down()
    {
        $this->dropTable('plans_problems');
    }
}