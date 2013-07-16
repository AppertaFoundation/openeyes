<?php

class m120306_161357_event_group_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('event_group',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
			'code' => 'varchar(2) COLLATE utf8_bin NOT NULL',
			'PRIMARY KEY (`id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->insert('event_group',array('name'=>'Clinical events','code'=>'Ci'));
		$this->insert('event_group',array('name'=>'Communication events','code'=>'Co'));
		$this->insert('event_group',array('name'=>'Investigation events','code'=>'In'));
		$this->insert('event_group',array('name'=>'Image events','code'=>'Im'));
		$this->insert('event_group',array('name'=>'Treatment events','code'=>'Tr'));
		$this->insert('event_group',array('name'=>'Drug events','code'=>'Dr'));
		$this->insert('event_group',array('name'=>'Miscellaneous','code'=>'Mi'));

		$this->addColumn('event_type','event_group_id','int(10) unsigned NULL');

		$this->update('event_type',array('event_group_id'=>1),'id in (1,2,3,15,16,18)');
		$this->update('event_type',array('event_group_id'=>2),'id in (17,21,22,23)');
		$this->update('event_type',array('event_group_id'=>3),'id in (13)');
		$this->update('event_type',array('event_group_id'=>3,'name'=>'hfa'),'id = 8');
		$this->update('event_type',array('event_group_id'=>4),'id in (5,6,7,9,10,11,12)');
		$this->update('event_type',array('event_group_id'=>5),'id in (4,20,25)');
		$this->update('event_type',array('event_group_id'=>6),'id in (14,19)');
		$this->delete('event_type','id = 24');

		$this->alterColumn('event_type','event_group_id','int(10) unsigned NOT NULL');
		$this->createIndex('event_type_event_group_id_fk','event_type','event_group_id');
		$this->addForeignKey('event_type_event_group_id_fk','event_type','event_group_id','event_group','id');
	}

	public function down()
	{
		$this->dropForeignKey('event_type_event_group_id_fk','event_type');
		$this->dropIndex('event_type_event_group_id_fk','event_type');
		$this->dropColumn('event_type','event_group_id');
		$this->dropTable('event_group');

		$this->update('event_type',array('name'=>'field'),'id = 8');
		$this->insert('event_type',array('id'=>24,'name'=>'example'));
	}
}
