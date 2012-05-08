<?php

class m120508_152303_put_user_contact_assignment_table_back extends CDbMigration
{
	public function up()
	{
		$this->createTable('user_contact_assignment',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'user_id' => 'int(10) unsigned NOT NULL',
				'contact_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'last_modified_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'created_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'created_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
				'PRIMARY KEY (`id`)',
				'UNIQUE KEY `user_id` (`user_id`)',
				'UNIQUE KEY `contact_id` (`contact_id`)',
				'KEY `user_contact_assignment_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `user_contact_assignment_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `user_contact_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `user_contact_assignment_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `user_contact_assignment_ibfk_2` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`)',
				'CONSTRAINT `user_contact_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			),'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		foreach ($this->dbConnection->createCommand()->select('contact.*')->from('contact')->where('parent_class=:parent_class',array(':parent_class'=>'User'))->queryAll() as $contact) {
			$contact2 = $this->dbConnection->createCommand()->select('contact.*')->from('contact')->where('parent_class!=:parent_class and nick_name=:nick_name and primary_phone=:primary_phone and title=:title and first_name=:first_name and last_name=:last_name and qualifications=:qualifications',array(':parent_class'=>'User',':nick_name'=>$contact['nick_name'],':primary_phone'=>$contact['primary_phone'],':title'=>$contact['title'],':first_name'=>$contact['first_name'],':last_name'=>$contact['last_name'],':qualifications'=>$contact['qualifications']))->queryRow();

			$this->insert('user_contact_assignment',array('user_id'=>$contact['parent_id'],'contact_id'=>$contact2['id']));

			$this->delete('contact','id='.$contact['id']);
		}
	}

	public function down()
	{
		foreach ($this->dbConnection->createCommand()->select('user_id, contact_id')->from('user_contact_assignment')->queryAll() as $uca) {
			$contact = $this->dbConnection->createCommand()->select('*')->from('contact')->where('id=:id',array(':id'=>$uca['contact_id']))->queryRow();

			if ($contact['parent_class'] && $contact['parent_class'] != 'User') {

				// Contact is already parented to another object so we need to clone it for the user

				unset($contact['id']);
				$contact['parent_class'] = 'User';
				$contact['parent_id'] = $uca['user_id'];

				$this->insert('contact',$contact);

				$contact = $this->dbConnection->createCommand()->select('*')->from('contact')->order('id desc')->queryRow();
			}
		}

		$this->dropTable('user_contact_assignment');
	}
}
