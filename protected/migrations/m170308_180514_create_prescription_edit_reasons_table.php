<?php

class m170308_180514_create_prescription_edit_reasons_table extends CDbMigration
{
	/*
    public function up()
	{

	}

	public function down()
	{

	}
	*/


	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	    $this->createTable('ophdrprescription_edit_reasons',
            array(
                'id' => 'pk',
                'caption' => 'string NOT NULL',
                'display_order' => 'tinyint(4) NOT NULL DEFAULT 0',
                'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1'
            ));

	    $this->addColumn('et_ophdrprescription_details', 'edit_reason_id', 'int');
	    $this->addForeignKey(
	        'et_ophdrprescription_details_edit_reason_fk',
            'et_ophdrprescription_details',
            'edit_reason_id',
            'ophdrprescription_edit_reasons',
            'id'
        );
	}

	public function safeDown()
	{
        $this->dropForeignKey(
            'et_ophdrprescription_details_edit_reason_fk',
            'et_ophdrprescription_details');
	    $this->dropColumn('et_ophdrprescription_details', 'edit_reason_id');
	    $this->delete('ophdrprescription_edit_reasons');
	}

}