<?php

class m120615_102526_language_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('language',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(32) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `language_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `language_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `language_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `language_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->insert('language',array('name'=>'Afrikaans'));
		$this->insert('language',array('name'=>'Albanian'));
		$this->insert('language',array('name'=>'Arabic'));
		$this->insert('language',array('name'=>'Belarusian'));
		$this->insert('language',array('name'=>'Bulgarian'));
		$this->insert('language',array('name'=>'Catalan'));
		$this->insert('language',array('name'=>'Chinese'));
		$this->insert('language',array('name'=>'Croatian'));
		$this->insert('language',array('name'=>'Czech'));
		$this->insert('language',array('name'=>'Danish'));
		$this->insert('language',array('name'=>'Dutch'));
		$this->insert('language',array('name'=>'English'));
		$this->insert('language',array('name'=>'Estonian'));
		$this->insert('language',array('name'=>'Filipino'));
		$this->insert('language',array('name'=>'Finnish'));
		$this->insert('language',array('name'=>'French'));
		$this->insert('language',array('name'=>'Galician'));
		$this->insert('language',array('name'=>'German'));
		$this->insert('language',array('name'=>'Greek'));
		$this->insert('language',array('name'=>'Haitian Creole'));
		$this->insert('language',array('name'=>'Hebrew'));
		$this->insert('language',array('name'=>'Hindi'));
		$this->insert('language',array('name'=>'Hungarian'));
		$this->insert('language',array('name'=>'Icelandic'));
		$this->insert('language',array('name'=>'Indonesian'));
		$this->insert('language',array('name'=>'Irish'));
		$this->insert('language',array('name'=>'Italian'));
		$this->insert('language',array('name'=>'Japanese'));
		$this->insert('language',array('name'=>'Korean'));
		$this->insert('language',array('name'=>'Latvian'));
		$this->insert('language',array('name'=>'Lithuanian'));
		$this->insert('language',array('name'=>'Macedonian'));
		$this->insert('language',array('name'=>'Malay'));
		$this->insert('language',array('name'=>'Maltese'));
		$this->insert('language',array('name'=>'Norwegian'));
		$this->insert('language',array('name'=>'Persian'));
		$this->insert('language',array('name'=>'Polish'));
		$this->insert('language',array('name'=>'Portuguese'));
		$this->insert('language',array('name'=>'Romanian'));
		$this->insert('language',array('name'=>'Russian'));
		$this->insert('language',array('name'=>'Serbian'));
		$this->insert('language',array('name'=>'Slovak'));
		$this->insert('language',array('name'=>'Slovenian'));
		$this->insert('language',array('name'=>'Spanish'));
		$this->insert('language',array('name'=>'Swahili'));
		$this->insert('language',array('name'=>'Swedish'));
		$this->insert('language',array('name'=>'Thai'));
		$this->insert('language',array('name'=>'Turkish'));
		$this->insert('language',array('name'=>'Ukrainian'));
		$this->insert('language',array('name'=>'Vietnamese'));
		$this->insert('language',array('name'=>'Welsh'));
		$this->insert('language',array('name'=>'Yiddish'));
	}

	public function down()
	{
		$this->dropTable('language');
	}
}
