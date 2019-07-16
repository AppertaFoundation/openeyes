<?php

class m190502_125723_create_operation_booking_on_hold_reasons extends OEMigration
{
    public function up()
    {
        $this->insert('ophtroperationbooking_operation_status', ['name' => 'On-Hold']);
        $this->createOETable('ophtroperationbooking_operation_on_hold_reason',[
            'id' => 'pk',
            'reason' => 'varchar(100) NOT NULL',
            'display_order' => 'tinyint(3) unsigned DEFAULT \'0\'',
        ],true);

        $this->insertMultiple('ophtroperationbooking_operation_on_hold_reason', [
            ['reason' => 'Uncontrolled hypertension', 'display_order' => 1],
            ['reason' => 'Uncontrolled blood sugar', 'display_order' => 2],
            ['reason' => 'Recent MI', 'display_order' => 3],
            ['reason' => 'Recent CVA', 'display_order' => 4],
            ['reason' => 'Too unwell', 'display_order' => 5],
            ['reason' => 'Blepharitis', 'display_order' => 6],
            ['reason' => 'Awaiting surgery elsewhere', 'display_order' => 7],
            ['reason' => 'Under investigations', 'display_order' => 8],
            ['reason' => 'Still under post op review - 1st eye', 'display_order' => 9],
            ['reason' => 'Out of the country', 'display_order' => 10],
            ['reason' => 'Bereavement', 'display_order' => 11],
    ]);

        $this->addColumn('et_ophtroperationbooking_operation', 'on_hold_reason', 'varchar(100) NULL');
        $this->addColumn('et_ophtroperationbooking_operation_version', 'on_hold_reason', 'varchar(100) NULL');
        $this->addColumn('et_ophtroperationbooking_operation', 'on_hold_comment', 'varchar(200) NULL');
        $this->addColumn('et_ophtroperationbooking_operation_version', 'on_hold_comment', 'varchar(200) NULL');
    }

    public function down()
    {
        $this->dropColumn('et_ophtroperationbooking_operation', 'on_hold_comment');
        $this->dropColumn('et_ophtroperationbooking_operation_version', 'on_hold_comment');
        $this->dropColumn('et_ophtroperationbooking_operation', 'on_hold_reason');
        $this->dropColumn('et_ophtroperationbooking_operation_version', 'on_hold_reason');
        $this->delete('ophtroperationbooking_operation_status', 'name = "On-Hold"');

        $this->dropOETable('ophtroperationbooking_operation_on_hold_reason', true);
    }
}