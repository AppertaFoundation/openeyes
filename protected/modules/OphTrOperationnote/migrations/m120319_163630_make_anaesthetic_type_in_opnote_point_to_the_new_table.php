<?php

class m120319_163630_make_anaesthetic_type_in_opnote_point_to_the_new_table extends CDbMigration
{
	public function up()
	{
		$this->update('et_ophtroperationnote_procedurelist',array('anaesthetic_type'=>5),'anaesthetic_type=4');
		$this->update('et_ophtroperationnote_procedurelist',array('anaesthetic_type'=>4),'anaesthetic_type=3');
		$this->update('et_ophtroperationnote_procedurelist',array('anaesthetic_type'=>3),'anaesthetic_type=2');
		$this->update('et_ophtroperationnote_procedurelist',array('anaesthetic_type'=>2),'anaesthetic_type=1');
		$this->update('et_ophtroperationnote_procedurelist',array('anaesthetic_type'=>1),'anaesthetic_type=0');

		$this->renameColumn('et_ophtroperationnote_procedurelist','anaesthetic_type','anaesthetic_type_id');
		$this->alterColumn('et_ophtroperationnote_procedurelist','anaesthetic_type_id',"int(10) unsigned NOT NULL DEFAULT '1'");
		$this->createIndex('et_ophtroperationnote_procedurelist_anaesthetic_type_id_fk','et_ophtroperationnote_procedurelist','anaesthetic_type_id');
		$this->addForeignKey('et_ophtroperationnote_procedurelist_anaesthetic_type_id_fk','et_ophtroperationnote_procedurelist','anaesthetic_type_id','anaesthetic_type','id');
	}

	public function down()
	{
		$this->dropForeignKey('et_ophtroperationnote_procedurelist_anaesthetic_type_id_fk','et_ophtroperationnote_procedurelist');
		$this->dropIndex('et_ophtroperationnote_procedurelist_anaesthetic_type_id_fk','et_ophtroperationnote_procedurelist');
		$this->alterColumn('et_ophtroperationnote_procedurelist','anaesthetic_type_id',"tinyint(1) unsigned DEFAULT '0'");
		$this->renameColumn('et_ophtroperationnote_procedurelist','anaesthetic_type_id','anaesthetic_type');

		$this->update('et_ophtroperationnote_procedurelist',array('anaesthetic_type'=>0),'anaesthetic_type=1');
		$this->update('et_ophtroperationnote_procedurelist',array('anaesthetic_type'=>1),'anaesthetic_type=2');
		$this->update('et_ophtroperationnote_procedurelist',array('anaesthetic_type'=>2),'anaesthetic_type=3');
		$this->update('et_ophtroperationnote_procedurelist',array('anaesthetic_type'=>3),'anaesthetic_type=4');
		$this->update('et_ophtroperationnote_procedurelist',array('anaesthetic_type'=>4),'anaesthetic_type=5');
	}
}
