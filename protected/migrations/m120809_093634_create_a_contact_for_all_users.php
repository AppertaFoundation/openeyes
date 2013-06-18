<?php

class m120809_093634_create_a_contact_for_all_users extends CDbMigration
{
	public function up()
	{
		foreach (Yii::app()->db->createCommand()->select("*")->from("user")->queryAll() as $user) {
			if (!$uca = Yii::app()->db->createCommand()->select("*")->from("user_contact_assignment")->where("user_id=:user_id",array(':user_id'=>$user['id']))->queryRow()) {
				$this->insert('contact',array(
					'title' => $user['title'],
					'first_name' => $user['first_name'],
					'title' => $user['last_name'],
					'qualifications' => $user['qualifications'],
				));

				$contact_id = Yii::app()->db->createCommand()->select("max(id)")->from("contact")->queryScalar();

				$this->insert('user_contact_assignment',array(
					'user_id' => $user['id'],
					'contact_id' => $contact_id,
				));
			}
		}
	}

	public function down()
	{
	}
}
