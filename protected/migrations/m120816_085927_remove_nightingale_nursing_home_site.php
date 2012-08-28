<?php

class m120816_085927_remove_nightingale_nursing_home_site extends CDbMigration
{
	public function up()
	{
		$this->delete('site_subspecialty_anaesthetic_agent_default','site_id=13');
		$this->delete('site_subspecialty_anaesthetic_agent','site_id=13');
		$this->delete('site_subspecialty_operative_device','site_id=13');
		if (Yii::app()->db->schema->getTable('et_ophcocorrespondence_letter_macro')) {
			$this->delete('et_ophcocorrespondence_letter_macro','site_id=13');
		}
		if (Yii::app()->db->schema->getTable('et_ophcocorrespondence_letter_string')) {
			$this->delete('et_ophcocorrespondence_letter_string','site_id=13');
		}
		if (Yii::app()->db->schema->getTable('et_ophtroperationnote_site_subspecialty_postop_instructions')) {
			$this->delete('et_ophtroperationnote_site_subspecialty_postop_instructions','site_id=13');
		}
		if (Yii::app()->db->schema->getTable('et_ophtroperationnote_postop_site_subspecialty_drug')) {
			$this->delete('et_ophtroperationnote_postop_site_subspecialty_drug','site_id=13');
		}
		$this->delete('site','id=13');
	}

	public function down()
	{
		$this->insert('site',array('id'=>13,'name'=>'Moorfields at Nightingale House Care Home, Wandsworth','code'=>'A1','short_name'=>'Wandsworth'));
	}
}
