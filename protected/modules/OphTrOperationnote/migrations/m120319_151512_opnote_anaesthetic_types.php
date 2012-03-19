<?php

class m120319_151512_opnote_anaesthetic_types extends CDbMigration
{
	public function up()
	{
		$to = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name',array(':name'=>'Topical'))->queryRow();
		$lac = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name',array(':name'=>'LAC'))->queryRow();
		$la = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name',array(':name'=>'LA'))->queryRow();
		$las = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name',array(':name'=>'LAS'))->queryRow();
		$ga = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name',array(':name'=>'GA'))->queryRow();

		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name',array(':name'=>'procedurelist'))->queryRow();

		$this->insert('element_type_anaesthetic_type',array('element_type_id'=>$element_type['id'],'anaesthetic_type_id'=>$to['id'],'display_order'=>1));
		$this->insert('element_type_anaesthetic_type',array('element_type_id'=>$element_type['id'],'anaesthetic_type_id'=>$la['id'],'display_order'=>2));
		$this->insert('element_type_anaesthetic_type',array('element_type_id'=>$element_type['id'],'anaesthetic_type_id'=>$lac['id'],'display_order'=>3));
		$this->insert('element_type_anaesthetic_type',array('element_type_id'=>$element_type['id'],'anaesthetic_type_id'=>$las['id'],'display_order'=>4));
		$this->insert('element_type_anaesthetic_type',array('element_type_id'=>$element_type['id'],'anaesthetic_type_id'=>$ga['id'],'display_order'=>5));
	}

	public function down()
	{
	}
}
