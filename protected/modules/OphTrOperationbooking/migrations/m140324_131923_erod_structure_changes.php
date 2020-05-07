<?php

class m140324_131923_erod_structure_changes extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophtroperationbooking_operation_erod', 'booking_id', 'int(10) unsigned');
        $this->addForeignKey(
            'ophtroperationbooking_operation_booking_bui_fk',
            'ophtroperationbooking_operation_erod',
            'booking_id',
            'ophtroperationbooking_operation_booking',
            'id'
        );

        $cmd = $this->dbConnection->createCommand('select erod.id as erod_id, op.id as op_id, book.id as book_id from ophtroperationbooking_operation_erod erod
left outer join et_ophtroperationbooking_operation op on erod.element_id = op.id
left outer join (
	select element_id, min(created_date) as min_created_date
	from ophtroperationbooking_operation_booking
	group by element_id
	) bookm
on bookm.element_id = op.id
left outer join ophtroperationbooking_operation_booking book on bookm.element_id = book.element_id
and bookm.min_created_date = book.created_date');
        foreach ($cmd->queryAll() as $row) {
            $this->dbConnection->createCommand()->update('ophtroperationbooking_operation_erod', array('booking_id' => $row['book_id']), 'id = :eid', array(':eid' => $row['erod_id']));
        }

        $this->dropForeignKey('ophtroperationbooking_operation_erod_element_id_fk', 'ophtroperationbooking_operation_erod');
        $this->dropColumn('ophtroperationbooking_operation_erod', 'element_id');
    }

    public function down()
    {
        $this->addColumn('ophtroperationbooking_operation_erod', 'element_id', 'int(10) unsigned');
        $this->addForeignKey(
            'ophtroperationbooking_operation_erod_element_id_fk',
            'ophtroperationbooking_operation_erod',
            'element_id',
            'et_ophtroperationbooking_operation',
            'id'
        );

        $cmd = $this->dbConnection->createCommand('select op.id as op_id, erod.id as erod_id, book.id as book_id from et_ophtroperationbooking_operation op
left outer join (
	select element_id, min(created_date) as min_created_date
	from ophtroperationbooking_operation_booking
	group by element_id
	) bookm
on bookm.element_id = op.id
left outer join ophtroperationbooking_operation_booking book on bookm.element_id = book.element_id
and bookm.min_created_date = book.created_date
left outer join ophtroperationbooking_operation_erod erod on erod.booking_id = book.id
');
        foreach ($cmd->queryAll() as $row) {
            $this->dbConnection->createCommand()->update('ophtroperationbooking_operation_erod', array('element_id' => $row['op_id']), 'id = :eid', array(':eid' => $row['erod_id']));
        }
        $this->dropForeignKey(
            'ophtroperationbooking_operation_booking_bui_fk',
            'ophtroperationbooking_operation_erod'
        );
        $this->dropColumn('ophtroperationbooking_operation_erod', 'booking_id');
        $this->delete('ophtroperationbooking_operation_erod', 'element_id is null');
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
