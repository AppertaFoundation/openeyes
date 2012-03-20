<?php

class m120319_162220_eo_anaesthetic_type_default_value extends CDbMigration
{
	public function up()
	{
		$this->renameColumn('element_operation','anaesthetic_type','anaesthetic_type_id');
		$this->alterColumn('element_operation','anaesthetic_type_id',"int(10) unsigned NOT NULL DEFAULT '1'");
		$this->createIndex('element_operation_anaesthetic_type_id_fk','element_operation','anaesthetic_type_id');
		$this->addForeignKey('element_operation_anaesthetic_type_id_fk','element_operation','anaesthetic_type_id','anaesthetic_type','id');
	}

	public function down()
	{
		$this->dropForeignKey('element_operation_anaesthetic_type_id_fk','element_operation');
		$this->dropIndex('element_operation_anaesthetic_type_id_fk','element_operation');
		$this->alterColumn('element_operation','anaesthetic_type_id',"tinyint(1) unsigned DEFAULT '0'");
		$this->renameColumn('element_operation','anaesthetic_type_id','anaesthetic_type');
	}
}
