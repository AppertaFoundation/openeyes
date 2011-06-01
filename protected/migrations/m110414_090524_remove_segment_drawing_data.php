<?php

class m110414_090524_remove_segment_drawing_data extends CDbMigration
{
	public function up()
	{
		// retrieve the element_type id's for these classnames
		$this->delete('element_type', 'class_name = :classname', array(':classname' => 'ElementAnteriorSegmentDrawing'));
		$this->delete('element_type', 'class_name = :classname', array(':classname' => 'ElementPosteriorSegmentDrawing'));
	}

	public function down()
	{
		$this->insert('element_type',
			array(
				'name' => 'Anterior Segment Drawing',
				'class_name' => 'ElementAnteriorSegmentDrawing',
			)
		);
		$this->insert('element_type',
			array(
				'name' => 'Posterior Segment Drawing',
				'class_name' => 'ElementPosteriorSegmentDrawing',
			)
		);
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
