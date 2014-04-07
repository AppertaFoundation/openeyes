<?php

class m140204_134240_systemic_diagnosis_shortcode extends CDbMigration
{
	public function up()
	{
		$this->insert(
			'patient_shortcode',
			array(
				'default_code' => 'syd',
				'code' => 'syd',
				'description' => 'Systemic diagnoses',
			)
		);
	}

	public function down()
	{
		$this->delete('patient_shortcode', 'description = ?', array('Systemic diagnoses'));
	}
}