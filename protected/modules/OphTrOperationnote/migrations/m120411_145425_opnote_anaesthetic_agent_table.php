<?php

class m120411_145425_opnote_anaesthetic_agent_table extends CDbMigration
{
	public function up()
	{
		$this->dropForeignKey('et_ophtroperationnote_paa_procedurelist_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_agent');
		$this->dropIndex('et_ophtroperationnote_paa_procedurelist_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_agent');
		$this->dropColumn('et_ophtroperationnote_anaesthetic_anaesthetic_agent','procedurelist_id');

		$this->addColumn('et_ophtroperationnote_anaesthetic_anaesthetic_agent','et_ophtroperationnote_anaesthetic_id','int(10) unsigned NOT NULL');
		$this->createIndex('et_ophtroperationnote_paa_anaesthetic_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_agent','et_ophtroperationnote_anaesthetic_id');
		$this->addForeignKey('et_ophtroperationnote_paa_anaesthetic_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_agent','et_ophtroperationnote_anaesthetic_id','et_ophtroperationnote_anaesthetic','id');
	}

	public function down()
	{
		$this->dropForeignKey('et_ophtroperationnote_paa_anaesthetic_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_agent');
		$this->dropIndex('et_ophtroperationnote_paa_anaesthetic_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_agent');
		$this->dropColumn('et_ophtroperationnote_anaesthetic_anaesthetic_agent','et_ophtroperationnote_anaesthetic_id');

		$this->addColumn('et_ophtroperationnote_anaesthetic_anaesthetic_agent','procedurelist_id','int(10) unsigned NOT NULL');
		$this->createIndex('et_ophtroperationnote_paa_procedurelist_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_agent','procedurelist_id');
		$this->addForeignKey('et_ophtroperationnote_paa_procedurelist_id_fk','et_ophtroperationnote_anaesthetic_anaesthetic_agent','procedurelist_id','et_ophtroperationnote_procedurelist','id');
	}
}
