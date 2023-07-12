<?php

class m230712_105443_populate_mailbox_id_for_existing_comments extends OEMigration
{
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		//For any comment that does not have a mailbox_id, set mailbox_id to the personal mailbox of the user that created it
		$this->execute("UPDATE ophcomessaging_message_comment omc 
			JOIN user u ON u.id = omc.created_user_id
			JOIN mailbox_user mu ON mu.user_id = u.id
			JOIN mailbox m ON m.id = mu.mailbox_id AND m.is_personal = 1
			SET omc.mailbox_id = m.id
			WHERE omc.mailbox_id IS NULL");
	}

	public function safeDown(){}
}