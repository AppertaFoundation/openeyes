<?php

class m130409_101123_give_non_consultant_users_a_staff_contact_label extends CDbMigration
{
	public function up()
	{
		$db = $this->getDbConnection();
		$staff = $this->getLabel('Staff');

		foreach ($db->createCommand()->select("*")->from("user")->queryAll() as $user) {
			$contact = $db->createCommand()->select("*")->from("contact")->where("id = :id",array(':id' => $user['contact_id']))->queryRow();

			if (!$contact['contact_label_id']) {
				$this->update('contact',array('contact_label_id'=>$staff['id']),"id={$contact['id']}");
			}
		}
	}

	public function getLabel($name)
	{
		$db = $this->getDbConnection();
		if ($label = $db->createCommand()->select("*")->from("contact_label")->where("name=:name",array(":name"=>$name))->queryRow()) {
			return $label;
		}

		$this->insert('contact_label',array('name'=>$name));

		return $this->getLabel($name);
	}

	public function down()
	{
	}
}
