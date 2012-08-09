<?php

class m120809_093634_create_a_contact_for_all_users extends CDbMigration
{
	public function up()
	{
		foreach (User::model()->findAll() as $user) {
			if (!$user->contact) {
				$contact = new Contact;
				$contact->title = $user->title;
				$contact->first_name = $user->first_name;
				$contact->last_name = $user->last_name;
				$contact->qualifications = $user->qualifications;
				$contact->save();

				$uca = new UserContactAssignment;
				$uca->user_id = $user->id;
				$uca->contact_id = $contact->id;
				$uca->save();
			}
		}
	}

	public function down()
	{
	}
}
