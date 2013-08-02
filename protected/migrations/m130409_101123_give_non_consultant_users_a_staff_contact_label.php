<?php

class m130409_101123_give_non_consultant_users_a_staff_contact_label extends CDbMigration
{
	public function up()
	{
		$staff = $this->getLabel('Staff');

		foreach (Yii::app()->db->createCommand()->select("*")->from("user")->queryAll() as $user) {
			$contact = Yii::app()->db->createCommand()->select("*")->from("contact")->where("id = :id",array(':id' => $user['contact_id']))->queryRow();

			if (!$contact['contact_label_id']) {
				$this->update('contact',array('contact_label_id'=>$staff['id']),"id={$contact['id']}");
			}
		}
	}

	public function getLabel($name)
	{
		if ($label = Yii::app()->db->createCommand()->select("*")->from("contact_label")->where("name=:name",array(":name"=>$name))->queryRow()) {
			return $label;
		}

		$this->insert('contact_label',array('name'=>$name));

		return $this->getLabel($name);
	}

	public function down()
	{
	}
}
