<?php

class m180404_085110_overnight_stay_required extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'ophtroperationbooking_overnight_stay_required',
            array(
                'id' => 'pk',
                'name' => 'string'
            )
        );

        $this->addColumn('et_ophtroperationbooking_operation', 'overnight_stay_required_id', 'INT');
        $this->addColumn('et_ophtroperationbooking_operation_version', 'overnight_stay_required_id', 'INT');

        $this->createIndex('idx_et_ophtroperationbooking_oosr', 'et_ophtroperationbooking_operation', 'overnight_stay_required_id');
        $this->addForeignKey('fk_et_ophtroperationbooking_oosr', 'et_ophtroperationbooking_operation', 'overnight_stay_required_id', 'ophtroperationbooking_overnight_stay_required', 'id');

        $this->insert('ophtroperationbooking_overnight_stay_required', array('name'=>'No'));
        $this->insert('ophtroperationbooking_overnight_stay_required', array('name'=>'Pre-op'));
        $this->insert('ophtroperationbooking_overnight_stay_required', array('name'=>'Post-op'));
        $this->insert('ophtroperationbooking_overnight_stay_required', array('name'=>'Both'));
    }

    public function down()
    {
        $this->dropForeignKey('fk_et_ophtroperationbooking_oosr', 'et_ophtroperationbooking_operation');
        $this->dropIndex('idx_et_ophtroperationbooking_oosr', 'et_ophtroperationbooking_operation');

        $this->dropColumn('et_ophtroperationbooking_operation', 'overnight_stay_required_id');
        $this->dropColumn('et_ophtroperationbooking_operation_version', 'overnight_stay_required_id');

        $this->dropTable('ophtroperationbooking_overnight_stay_required');
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