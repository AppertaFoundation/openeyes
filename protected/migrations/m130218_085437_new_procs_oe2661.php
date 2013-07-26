<?php

class m130218_085437_new_procs_oe2661 extends CDbMigration
{
	public function up()
	{
		$this->insert('proc',array('term'=>'Removal of eyelash','short_format'=>'Epilation','default_duration'=>5,'snomed_code'=>'398072007','snomed_term'=>'Removal of eyelash'));
		$this->insert('proc',array('term'=>'Removal of foreign body from conjunctiva','short_format'=>'Conj FB removal','default_duration'=>5,'snomed_code'=>'78362007','snomed_term'=>'Removal of foreign body from conjunctiva'));
		$this->insert('proc',array('term'=>'Excision of lesion of cornea','short_format'=>'Excision of corneal lesion','default_duration'=>10,'snomed_code'=>'75588007','snomed_term'=>'Excision of lesion of cornea'));
		$this->insert('proc',array('term'=>'Adjustment to corneal suture','short_format'=>'Adjust corneal suture','default_duration'=>5,'snomed_code'=>'172421008','snomed_term'=>'Adjustment to corneal suture'));
		$this->insert('proc',array('term'=>'Insertion of bandage contact lens','short_format'=>'Bandage lens','default_duration'=>10,'snomed_code'=>'428497007','snomed_term'=>'Insertion of therapeutic contact lens into cornea'));
		$this->insert('proc',array('term'=>'Removal of releasable suture following glaucoma surgery','short_format'=>'Removal of releasable suture','default_duration'=>10,'snomed_code'=>'426877004','snomed_term'=>'Removal of releasable suture following glaucoma surgery'));
		$this->insert('proc',array('term'=>'Reformation of anterior chamber','short_format'=>'Reform AC','default_duration'=>10,'snomed_code'=>'172517004','snomed_term'=>'Reformation of anterior chamber'));
		$this->insert('proc',array('term'=>'Topical local anaesthetic to eye','short_format'=>'Topical anaesthetic','default_duration'=>0,'snomed_code'=>'231346001','snomed_term'=>'Topical local anesthetic to eye'));
		$this->insert('proc',array('term'=>'Conjunctival swab','short_format'=>'Conj swab','default_duration'=>5,'snomed_code'=>'312855001','snomed_term'=>'Taking conjunctival swab'));
		$this->insert('proc',array('term'=>'Indocyanine green angiography','short_format'=>'ICG angiogram','default_duration'=>30,'snomed_code'=>'252823001','snomed_term'=>'Indocyanine green angiography'));
		$this->insert('proc',array('term'=>'B scan ultrasound of eye','short_format'=>'B scan','default_duration'=>20,'snomed_code'=>'241452002','snomed_term'=>'Orbital B scan'));
		$this->insert('proc',array('term'=>'Scanning laser ophthalmoscopy','short_format'=>'SLO','default_duration'=>20,'snomed_code'=>'252846004','snomed_term'=>'Scanning laser ophthalmoscopy'));
		$this->insert('proc',array('term'=>'Optical coherence tomography','short_format'=>'OCT','default_duration'=>15,'snomed_code'=>'392010000','snomed_term'=>'Optical coherence tomography'));
		$this->insert('proc',array('term'=>'Insertion of sustained release device into posterior segment of eye','short_format'=>'Insertion slow release','default_duration'=>80,'snomed_code'=>'428618008','snomed_term'=>'Insertion of sustained release device into posterior segment of eye'));

		$old = Procedure::model()->find('term=? and snomed_code=?',array('Bandage contact lens','416582002'));
		$new = Procedure::model()->find('term=? and snomed_code=?',array('Insertion of bandage contact lens','428497007'));

		$this->update('proc_opcs_assignment',array('proc_id'=>$new->id),"proc_id = $old->id");
		$this->update('proc_subspecialty_assignment',array('proc_id'=>$new->id),"proc_id = $old->id");

		if (isset(Yii::app()->modules['OphTrOperationnote']) && $this->hasTable('et_ophtroperationnote_procedure_element')) {
			$this->update('et_ophtroperationnote_procedure_element',array('procedure_id'=>$new->id),"procedure_id = $old->id");
		}

		$this->delete('proc',"id=$old->id");
	}

	public function down()
	{
		$this->insert('proc',array('term'=>'Bandage contact lens','short_format'=>'Bandage','default_duration'=>20,'snomed_code'=>'416582002','snomed_term'=>'Bandage contact lens'));

		$old = Procedure::model()->find('term=? and snomed_code=?',array('Bandage contact lens','416582002'));
		$new = Procedure::model()->find('term=? and snomed_code=?',array('Insertion of bandage contact lens','428497007'));

		$this->update('proc_opcs_assignment',array('proc_id'=>$old->id),"proc_id = $new->id");
		$this->update('proc_subspecialty_assignment',array('proc_id'=>$old->id),"proc_id = $new->id");

		if (isset(Yii::app()->modules['OphTrOperationnote']) && $this->hasTable('et_ophtroperationnote_procedure_element')) {
			$this->update('et_ophtroperationnote_procedure_element',array('procedure_id'=>$old->id),"procedure_id = $new->id");
		}

		$this->delete('proc',"term='Removal of eyelash' and snomed_code='398072007'");
		$this->delete('proc',"term='Removal of foreign body from conjunctiva' and snomed_code='78362007'");
		$this->delete('proc',"term='Excision of lesion of cornea' and snomed_code='75588007'");
		$this->delete('proc',"term='Adjustment to corneal suture' and snomed_code='172421008'");
		$this->delete('proc',"term='Insertion of bandage contact lens' and snomed_code='428497007'");
		$this->delete('proc',"term='Removal of releasable suture following glaucoma surgery' and snomed_code='426877004'");
		$this->delete('proc',"term='Reformation of anterior chamber' and snomed_code='172517004'");
		$this->delete('proc',"term='Topical local anaesthetic to eye' and snomed_code='231346001'");
		$this->delete('proc',"term='Conjunctival swab' and snomed_code='312855001'");
		$this->delete('proc',"term='Indocyanine green angiography' and snomed_code='252823001'");
		$this->delete('proc',"term='B scan ultrasound of eye' and snomed_code='241452002'");
		$this->delete('proc',"term='Scanning laser ophthalmoscopy' and snomed_code='252846004'");
		$this->delete('proc',"term='Optical coherence tomography' and snomed_code='392010000'");
		$this->delete('proc',"term='Insertion of sustained release device into posterior segment of eye' and snomed_code='428618008'");
	}

	public function hasTable($table)
	{
		foreach (Yii::app()->db->createCommand("show tables")->queryAll() as $row) {
			foreach ($row as $key => $value) {
				if ($value == $table) {
					return true;
				}
			}
		}
		return false;
	}
}
