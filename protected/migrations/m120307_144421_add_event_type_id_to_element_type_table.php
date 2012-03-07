<?php

class m120307_144421_add_event_type_id_to_element_type_table extends CDbMigration
{
	public function up()
	{
		// add event_type_id to element_type
		$this->addColumn('element_type','event_type_id','int(10) unsigned NOT NULL DEFAULT 1');

		// add display_order column to element_type
		$this->addColumn('element_type','display_order','int(10) unsigned NOT NULL DEFAULT 1');

		// retrieve the event_type_id for event_type 'Operation' (booking)
		$booking_event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name' => 'operation'))->queryRow();

		// set the right event_type_id and display_order settings for operation and operationnote - event-type-modules can set these for themselves in future
		$this->update('element_type',array('event_type_id'=> $booking_event_type['id'], 'display_order'=>'1'),"name='Diagnosis'");
		$this->update('element_type',array('event_type_id'=> $booking_event_type['id'], 'display_order'=>'2'),"name='Operation'");
	}

	public function down()
	{
		$this->dropColumn('element_type', 'event_type_id');
		$this->dropColumn('element_type', 'display_order');
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
