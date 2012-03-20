<?php

class m120320_153814_priority_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('priority',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(10) COLLATE utf8_bin DEFAULT NULL',
			'PRIMARY KEY (`id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->insert('priority',array('name'=>'Routine'));
		$this->insert('priority',array('name'=>'Urgent'));

		$this->renameColumn('element_operation','urgent','priority_id');
		$this->createIndex('element_operation_priority_id_fk','element_operation','priority_id');
		$this->alterColumn('element_operation','priority_id',"int(10) unsigned NOT NULL DEFAULT '1'");

		$this->update('element_operation',array('priority_id'=>2),'priority_id=1');
		$this->update('element_operation',array('priority_id'=>1),'priority_id=0');

		$this->addForeignKey('element_operation_priority_id_fk','element_operation','priority_id','priority','id');

		$this->createTable('element_type_priority',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'element_type_id' => 'int(10) unsigned NOT NULL',
			'priority_id' => 'int(10) unsigned NOT NULL',
			'display_order' => 'tinyint(1) unsigned NOT NULL DEFAULT 1',
			'PRIMARY KEY (`id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createIndex('element_type_priority_fk1','element_type_priority','element_type_id');
		$this->createIndex('element_type_priority_fk2','element_type_priority','priority_id');
		$this->addForeignKey('element_type_priority_fk1','element_type_priority','element_type_id','element_type','id');
		$this->addForeignKey('element_type_priority_fk2','element_type_priority','priority_id','priority','id');

		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name', array(':name'=>'Operation'))->queryRow();

		$this->insert('element_type_priority',array('element_type_id'=>$element_type['id'],'priority_id'=>1,'display_order'=>1));
		$this->insert('element_type_priority',array('element_type_id'=>$element_type['id'],'priority_id'=>2,'display_order'=>2));
	}

	public function down()
	{
		$this->dropForeignKey('element_type_priority_fk1','element_type_priority');
		$this->dropForeignKey('element_type_priority_fk2','element_type_priority');
		$this->dropIndex('element_type_priority_fk1','element_type_priority');
		$this->dropIndex('element_type_priority_fk2','element_type_priority');
		$this->dropTable('element_type_priority');

		$this->dropForeignKey('element_operation_priority_id_fk','element_operation');
		$this->dropIndex('element_operation_priority_id_fk','element_operation');
		$this->renameColumn('element_operation','priority_id','urgent');
		$this->alterColumn('element_operation','urgent',"tinyint(1) unsigned DEFAULT '0'");

		$this->update('element_operation',array('urgent'=>0),'urgent=1');
		$this->update('element_operation',array('urgent'=>1),'urgent=2');

		$this->dropTable('priority');
	}
}
