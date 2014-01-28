<?php

class m131128_083823_fix_broken_foreign_keys extends CDbMigration
{
	public function up()
	{
		$this->dropForeignKey('ethnic_group_created_user_id_fk','ethnic_group');
		$this->dropForeignKey('ethnic_group_last_modified_user_id_fk','ethnic_group');

		$this->addForeignKey('ethnic_group_created_user_id_fk','ethnic_group','created_user_id','user','id');
		$this->addForeignKey('ethnic_group_last_modified_user_id_fk','ethnic_group','last_modified_user_id','user','id');
	}

	public function down()
	{
		$this->dropForeignKey('ethnic_group_created_user_id_fk','ethnic_group');
		$this->dropForeignKey('ethnic_group_last_modified_user_id_fk','ethnic_group');

		$this->addForeignKey('ethnic_group_created_user_id_fk','ethnic_group','last_modified_user_id','user','id');
		$this->addForeignKey('ethnic_group_last_modified_user_id_fk','ethnic_group','created_user_id','user','id');
	}
}
