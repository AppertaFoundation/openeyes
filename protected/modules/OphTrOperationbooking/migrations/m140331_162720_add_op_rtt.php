<?php

class m140331_162720_add_op_rtt extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophtroperationbooking_operation', 'rtt_id', 'integer');
        $this->addForeignKey(
            'et_ophtroperationbooking_operation_rttui_fk',
            'et_ophtroperationbooking_operation',
            'rtt_id',
            'rtt',
            'id'
        );
    }

    public function down()
    {
        $this->dropColumn('et_ophtroperationbooking_operation', 'rtt_id');
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
