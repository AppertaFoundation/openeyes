<?php

class m120806_165500_add_indices_to_non_fk_relations extends CDbMigration
{
	public function up()
	{
		$this->createIndex('address_parent_index', 'address', 'parent_class,parent_id');
		$this->createIndex('contact_parent_index', 'contact', 'parent_class,parent_id');
	}

	public function down()
	{
		$this->dropIndex('address_parent_index', 'address');
		$this->dropIndex('contact_parent_index', 'contact');
	}

}
