<?php

class m130426_082237_trabectome_is_common_for_glaucoma extends CDbMigration
{
	public function up()
	{
		if ($proc = Yii::app()->db->createCommand()->select("id")->from("proc")->where('snomed_code=:snomed_code',array(':snomed_code'=>'31337'))->queryRow()) {
			if ($specialty = Yii::app()->db->createCommand()->select("id")->from("specialty")->where('code=:code',array(':code'=>'OPH'))->queryRow()) {
				if ($subspecialty = Yii::app()->db->createCommand()->select("id")->from("subspecialty")->where('specialty_id=:specialty_id and name=:name',array(':specialty_id'=>$specialty['id'],':name'=>'Glaucoma'))->queryRow()) {
					$this->insert('proc_subspecialty_assignment',array('proc_id'=>$proc['id'],'subspecialty_id'=>$subspecialty['id']));
				}
			}
		}
	}

	public function down()
	{
		if ($proc = Yii::app()->db->createCommand()->select("id")->from("proc")->where('snomed_code=:snomed_code',array(':snomed_code'=>'31337'))->queryRow()) {
			if ($specialty = Yii::app()->db->createCommand()->select("id")->from("specialty")->where('code=:code',array(':code'=>'OPH'))->queryRow()) {
				if ($subspecialty = Yii::app()->db->createCommand()->select("id")->from("subspecialty")->where('specialty_id=:specialty_id and name=:name',array(':specialty_id'=>$specialty['id'],':name'=>'Glaucoma'))->queryRow()) {
					$this->delete('proc_subspecialty_assignment',"proc_id={$proc['id']} and subspecialty_id={$subspecialty['id']}");
				}
			}
		}
	}
}
