<?php

class m140319_141456_add_referral_link extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophtroperationbooking_operation', 'referral_id', 'int(10) unsigned');
        $this->addForeignKey(
            'et_ophtroperationbooking_operation_refi_fk',
            'et_ophtroperationbooking_operation',
            'referral_id',
            'referral',
            'id'
        );
    }

    public function down()
    {
        $this->dropForeignKey('et_ophtroperationbooking_operation_refi_fk', 'et_ophtroperationbooking_operation');
        $this->dropColumn('et_ophtroperationbooking_operation', 'referral_id');
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
