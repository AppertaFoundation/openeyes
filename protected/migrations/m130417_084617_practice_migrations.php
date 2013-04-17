<?php

class m130417_084617_practice_migrations extends CDbMigration
{
	public function up()
	{
		//$this->addColumn('practice','contact_id','int(10) unsigned NOT NULL');

		foreach (Yii::app()->db->createCommand()->select("*")->from("practice")->queryAll() as $practice) {
			$address = Yii::app()->db->createCommand()->select("*")->from("address")->where("parent_class = :parent_class and parent_id = :parent_id",array(':parent_class'=>'Practice',':parent_id'=>$practice['id']))->queryRow();

			$this->insert('contact',array());
			$contact_id = Yii::app()->db->createCommand()->select("max(id)")->from("contact")->queryScalar();

			$this->update('practice',array('contact_id'=>$contact_id),"id={$practice['id']}");
			$this->update('contact',array('primary_phone'=>$practice['phone']),"id=$contact_id");

			if ($address) {
				$this->update('address',array('parent_class'=>'Contact','parent_id'=>$contact_id),"id={$address['id']}");
			}
		}

		$this->createIndex('practice_contact_id_fk','practice','contact_id');
		$this->addForeignKey('practice_contact_id_fk','practice','contact_id','contact','id');

		$this->delete('address',"parent_class = 'Practice'");
	}

	public function down()
	{
	}
}
