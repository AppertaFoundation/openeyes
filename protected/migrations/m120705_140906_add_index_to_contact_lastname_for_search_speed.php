<?php

class m120705_140906_add_index_to_contact_lastname_for_search_speed extends CDbMigration
{
	public function up()
	{
		$this->createIndex('contact_last_name_key','contact','last_name');
	}

	public function down()
	{
		$this->dropIndex('contact_last_name_key','contact');
	}
}
