<?php

class m220324_031212_add_examination_step_widget_view extends OEMigration
{
	public function up()
	{
		$this->update('pathway_step_type', array('widget_view' => 'examination'), 'long_name = "Examination"');
	}

	public function down()
	{
		$this->update('pathway_step_type', array('widget_view' => null), 'long_name = "Examination"');
	}
}
