<?php

class m130603_114507_normalise_audit_table extends CDbMigration
{
	public $lookup = array();

	public function up()
	{
		$this->createTable('audit_action', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `audit_action_lmui_fk` (`last_modified_user_id`)',
				'KEY `audit_action_cui_fk` (`created_user_id`)',
				'CONSTRAINT `audit_action_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `audit_action_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('audit_type', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `audit_type_lmui_fk` (`last_modified_user_id`)',
				'KEY `audit_type_cui_fk` (`created_user_id`)',
				'CONSTRAINT `audit_type_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `audit_type_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('audit_ipaddr', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(16) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `audit_ipaddr_lmui_fk` (`last_modified_user_id`)',
				'KEY `audit_ipaddr_cui_fk` (`created_user_id`)',
				'CONSTRAINT `audit_ipaddr_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `audit_ipaddr_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('audit_useragent', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(1024) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `audit_useragent_lmui_fk` (`last_modified_user_id`)',
				'KEY `audit_useragent_cui_fk` (`created_user_id`)',
				'CONSTRAINT `audit_useragent_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `audit_useragent_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('audit_server', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `audit_server_lmui_fk` (`last_modified_user_id`)',
				'KEY `audit_server_cui_fk` (`created_user_id`)',
				'CONSTRAINT `audit_server_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `audit_server_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		foreach (Yii::app()->db->createCommand()->select("distinct(action) as action")->from('audit')->queryAll() as $audit) {
			$action_id = $this->findObject('action',$audit['action']);
			$this->update('audit',array('action'=>$action_id),"action='{$audit['action']}'");
		}

		foreach (Yii::app()->db->createCommand()->select("distinct(target_type) as target_type")->from("audit")->queryAll() as $audit) {
			$type_id = $this->findObject('type',$audit['target_type']);
			$this->update('audit',array('target_type'=>$type_id),"target_type='{$audit['target_type']}'");
		}

		foreach (Yii::app()->db->createCommand()->select("distinct(remote_addr) as remote_addr")->from("audit")->queryAll() as $audit) {
			$ipaddr_id = $audit['remote_addr'] ? $this->findObject('ipaddr',$audit['remote_addr']) : null;
			$this->update('audit',array('remote_addr'=>$ipaddr_id),"remote_addr='{$audit['remote_addr']}'");
		}

		foreach (Yii::app()->db->createCommand()->select("distinct(http_user_agent) as http_user_agent")->from("audit")->queryAll() as $audit) {
			$useragent_id = $audit['http_user_agent'] ? $this->findObject('useragent',$audit['http_user_agent']) : null;
			$this->update('audit',array('http_user_agent'=>$useragent_id),"http_user_agent='{$audit['http_user_agent']}'");
		}

		foreach (Yii::app()->db->createCommand()->select("distinct(server_name) as server_name")->from("audit")->queryAll() as $audit) {
			$server_id = $audit['server_name'] ? $this->findObject('server',$audit['server_name']) : null;
			$this->update('audit',array('server_name'=>$server_id),"server_name='{$audit['server_name']}'");
		}

		$this->renameColumn('audit','action','action_id');
		$this->alterColumn('audit','action_id','int(10) unsigned NOT NULL');
		$this->createIndex('audit_action_id_fk','audit','action_id');
		$this->addForeignKey('audit_action_id_fk','audit','action_id','audit_action','id');

		$this->renameColumn('audit','target_type','type_id');
		$this->alterColumn('audit','type_id','int(10) unsigned NOT NULL');
		$this->createIndex('audit_type_id_fk','audit','type_id');
		$this->addForeignKey('audit_type_id_fk','audit','type_id','audit_type','id');

		$this->renameColumn('audit','remote_addr','ipaddr_id');
		$this->alterColumn('audit','ipaddr_id','int(10) unsigned NULL');
		$this->createIndex('audit_ipaddr_id_fk','audit','ipaddr_id');
		$this->addForeignKey('audit_ipaddr_id_fk','audit','ipaddr_id','audit_ipaddr','id');

		$this->renameColumn('audit','http_user_agent','useragent_id');
		$this->alterColumn('audit','useragent_id','int(10) unsigned NULL');
		$this->createIndex('audit_useragent_id_fk','audit','useragent_id');
		$this->addForeignKey('audit_useragent_id_fk','audit','useragent_id','audit_useragent','id');

		$this->renameColumn('audit','server_name','server_id');
		$this->alterColumn('audit','server_id','int(10) unsigned NULL');
		$this->createIndex('audit_server_id_fk','audit','server_id');
		$this->addForeignKey('audit_server_id_fk','audit','server_id','audit_server','id');
	}

	public function findObject($type,$name)
	{
		if (isset($this->lookup[$type][$name])) {
			return $this->lookup[$type][$name];
		}

		if ($object = Yii::app()->db->createCommand()->select("*")->from("audit_$type")->where('name=:name',array(':name'=>$name))->queryRow()) {
			$this->lookup[$type][$name] = $object['id'];
			return $object['id'];
		}

		$this->insert("audit_$type",array('name'=>$name));

		return $this->findObject($type,$name);
	}

	public function down()
	{
		$this->dropForeignKey('audit_server_id_fk','audit');
		$this->dropIndex('audit_server_id_fk','audit');
		$this->alterColumn('audit','server_id','varchar(255) COLLATE utf8_bin');
		$this->renameColumn('audit','server_id','server_name');

		$this->dropForeignKey('audit_useragent_id_fk','audit');
		$this->dropIndex('audit_useragent_id_fk','audit');
		$this->alterColumn('audit','useragent_id','varchar(255) COLLATE utf8_bin');
		$this->renameColumn('audit','useragent_id','http_user_agent');

		$this->dropForeignKey('audit_ipaddr_id_fk','audit');
		$this->dropIndex('audit_ipaddr_id_fk','audit');
		$this->alterColumn('audit','ipaddr_id','varchar(255) COLLATE utf8_bin');
		$this->renameColumn('audit','ipaddr_id','remote_addr');

		$this->dropForeignKey('audit_type_id_fk','audit');
		$this->dropIndex('audit_type_id_fk','audit');
		$this->alterColumn('audit','type_id','varchar(20) COLLATE utf8_bin NOT NULL');
		$this->renameColumn('audit','type_id','target_type');

		$this->dropForeignKey('audit_action_id_fk','audit');
		$this->dropIndex('audit_action_id_fk','audit');
		$this->alterColumn('audit','action_id','varchar(32) COLLATE utf8_bin NOT NULL');
		$this->renameColumn('audit','action_id','action');

		foreach (Yii::app()->db->createCommand()->select("distinct(action) as action")->from('audit')->queryAll() as $audit) {
			$action_id = $this->findObjectName('action',$audit['action']);
			$this->update('audit',array('action'=>$action_id),"action='{$audit['action']}'");
		}

		foreach (Yii::app()->db->createCommand()->select("distinct(target_type) as target_type")->from("audit")->queryAll() as $audit) {
			$type_id = $this->findObjectName('type',$audit['target_type']);
			$this->update('audit',array('target_type'=>$type_id),"target_type='{$audit['target_type']}'");
		}

		foreach (Yii::app()->db->createCommand()->select("distinct(remote_addr) as remote_addr")->from("audit")->queryAll() as $audit) {
			$ipaddr_id = $audit['remote_addr'] ? $this->findObjectName('ipaddr',$audit['remote_addr']) : null;
			$this->update('audit',array('remote_addr'=>$ipaddr_id),"remote_addr='{$audit['remote_addr']}'");
		}

		foreach (Yii::app()->db->createCommand()->select("distinct(http_user_agent) as http_user_agent")->from("audit")->queryAll() as $audit) {
			$useragent_id = $audit['http_user_agent'] ? $this->findObjectName('useragent',$audit['http_user_agent']) : null;
			$this->update('audit',array('http_user_agent'=>$useragent_id),"http_user_agent='{$audit['http_user_agent']}'");
		}

		foreach (Yii::app()->db->createCommand()->select("distinct(server_name) as server_name")->from("audit")->queryAll() as $audit) {
			$server_id = $audit['server_name'] ? $this->findObjectName('server',$audit['server_name']) : null;
			$this->update('audit',array('server_name'=>$server_id),"server_name='{$audit['server_name']}'");
		}

		$this->dropTable('audit_server');
		$this->dropTable('audit_useragent');
		$this->dropTable('audit_ipaddr');
		$this->dropTable('audit_type');
		$this->dropTable('audit_action');
	}

	public function findObjectName($type,$id)
	{
		if (isset($this->lookup[$type][$id])) {
			return $this->lookup[$type][$id];
		}

		if ($object = Yii::app()->db->createCommand()->select("*")->from("audit_$type")->where('id=:id',array(':id'=>$id))->queryRow()) {
			$this->lookup[$type][$id] = $object['name'];
			return $object['name'];
		}

		throw new Exception("Object not found: $type / $id\n");
	}
}
