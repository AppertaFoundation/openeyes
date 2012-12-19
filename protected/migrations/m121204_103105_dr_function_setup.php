cd <?php

class m121204_103105_dr_function_setup extends CDbMigration
{
	public function up()
	{
		
		if (Yii::app()->hasModule('OphCoCorrespondence')) {
			echo "\n installing DRS correspondence macros ...\n";
			$mr = $this->dbConnection->createCommand()->select('id')->from('subspecialty')->where('ref_spec=:code', array(':code'=>'MR'))->queryRow();
			$mr_id = $mr['id'];
			
			// Follow up letter macro
			if (!$lm = SubspecialtyLetterMacro::model()->find('name=? and subspecialty_id=?', array('Follow up', $mr_id))) {
				$lm = new SubspecialtyLetterMacro;
			}
			$lm->name = 'Follow up';
			$lm->subspecialty_id = $mr_id;
			$lm->episode_status_id = 5;
			$lm->recipient_patient = 0;
			$lm->recipient_doctor = 1;
			$lm->use_nickname = 1;
			$lm->body = "Diagnosis:
Right eye:    [crd]([nrr]/[nrm])
Left eye:     [cld]([nlr]/[nlm])
[dmt]
					
Visual acuity: [vbb] 
Laser management: [lmp]
					
Comments: [pro] has been advised of the importance of optimal blood sugar and blood pressure control in reducing the risk of retinopathy and maculopathy worsening. The importance of regular follow-up has been emphasised. Other points: [lmc].
[pro] will be reviewed in [fup]";
			$lm->cc_patient = 1;
			$lm->display_order = 1;
			$lm->save();
			
			// Discharge letter macro
			if (!$lm = SubspecialtyLetterMacro::model()->find('name=? and subspecialty_id=?', array('Discharge', $mr_id))) {
				$lm = new SubspecialtyLetterMacro;
			}
			$lm->name = 'Discharge';
			$lm->subspecialty_id = $mr_id;
			$lm->episode_status_id = 6;
			$lm->recipient_patient = 0;
			$lm->recipient_doctor = 1;
			$lm->use_nickname = 1;
			$lm->body = "Diagnosis:
Right eye:    [crd]([nrr]/[nrm])
Left eye:     [cld]([nlr]/[nlm])
[dmt]

Visual acuity:  [vbb] 
Laser management: [lmp]
					
Comments: [pro] has been advised of the importance of optimal blood sugar and blood pressure control in reducing the risk of retinopathy and maculopathy worsening. The importance of regular follow-up has been emphasised. Other points: [lmc].
[pro] has been referred to [pos] PCT's diabetic retinopathy screening programme who will review [obj] in one year's time.";
			$lm->cc_patient = 1;
			$lm->display_order = 1;
			$lm->save();
			
		}
		
			
	}

	public function down()
	{
		$sub_spec = $this->dbConnection->createCommand()->select('id')->from('subspecialty')->where('ref_spec=:ref',array(':ref'=>"MR"))->queryRow();
		
		// remove the letter macros
		$this->delete('et_ophcocorrespondence_subspecialty_letter_macro', "subspecialty_id=:id", array(":id" => $sub_spec['id']));
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