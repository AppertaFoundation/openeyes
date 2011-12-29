<?php

class m111229_094554_add_site_id_to_element_operation_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('element_operation','site_id','int(10) unsigned NOT NULL DEFAULT 1');
		$this->addForeignKey('element_operation_site_id_fk','element_operation','site_id','site','id');
	}

	public function down()
	{
		$this->dropForeignKey('element_operation_site_id_fk','element_operation');
		$this->dropColumn('element_operation','site_id');
	}
}
