<?php

class m181126_102536_set_vte_assessment_as_default_true extends CDbMigration
{
	public function up()
	{
		$this->update('element_type', ['default' => 1], 'class_name = "Element_OphTrOperationnote_VteAssessment" AND event_type_id = 4');
	}

	public function down()
	{
		$this->update('element_type', ['default' => 0], 'class_name = "Element_OphTrOperationnote_VteAssessment" AND event_type_id = 4');
	}
}