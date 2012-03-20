<?php

class m120319_143521_generic_anaesthetic_type_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('anaesthetic_type',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => "varchar(255) NOT NULL DEFAULT ''",
			'code' => "varchar(3) NOT NULL DEFAULT ''",
			'PRIMARY KEY (`id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->insert('anaesthetic_type',array('id'=>1, 'name'=>'Topical','code'=>'Topical'));
		$this->insert('anaesthetic_type',array('id'=>2, 'name'=>'Local with cover','code'=>'LAC'));
		$this->insert('anaesthetic_type',array('id'=>3, 'name'=>'Local','code'=>'LA'));
		$this->insert('anaesthetic_type',array('id'=>4, 'name'=>'Local with sedation','code'=>'LAS'));
		$this->insert('anaesthetic_type',array('id'=>5, 'name'=>'General','code'=>'GA'));

		$this->createTable('element_type_anaesthetic_type',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'element_type_id' => 'int(10) unsigned NOT NULL',
			'anaesthetic_type_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createIndex('element_type_anaesthetic_type_fk1','element_type_anaesthetic_type','element_type_id');
		$this->createIndex('element_type_anaesthetic_type_fk2','element_type_anaesthetic_type','anaesthetic_type_id');
		$this->addForeignKey('element_type_anaesthetic_type_fk1','element_type_anaesthetic_type','element_type_id','element_type','id');
		$this->addForeignKey('element_type_anaesthetic_type_fk2','element_type_anaesthetic_type','anaesthetic_type_id','anaesthetic_type','id');

		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name', array(':name'=>'Operation'))->queryRow();

		$this->insert('element_type_anaesthetic_type',array('element_type_id'=>$element_type['id'],'anaesthetic_type_id'=>1));
		$this->insert('element_type_anaesthetic_type',array('element_type_id'=>$element_type['id'],'anaesthetic_type_id'=>2));
		$this->insert('element_type_anaesthetic_type',array('element_type_id'=>$element_type['id'],'anaesthetic_type_id'=>3));
		$this->insert('element_type_anaesthetic_type',array('element_type_id'=>$element_type['id'],'anaesthetic_type_id'=>4));
		$this->insert('element_type_anaesthetic_type',array('element_type_id'=>$element_type['id'],'anaesthetic_type_id'=>5));
	}

	public function down()
	{
		$this->dropForeignKey('element_type_anaesthetic_type_fk1','element_type_anaesthetic_type');
		$this->dropForeignKey('element_type_anaesthetic_type_fk2','element_type_anaesthetic_type');
		$this->dropIndex('element_type_anaesthetic_type_fk1','element_type_anaesthetic_type');
		$this->dropIndex('element_type_anaesthetic_type_fk2','element_type_anaesthetic_type');
		$this->dropTable('element_type_anaesthetic_type');
		$this->dropTable('anaesthetic_type');
	}
}
