<?php

class m120328_161726_event_issue_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('issue',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(1024) COLLATE utf8_bin DEFAULT NULL',
			'PRIMARY KEY (`id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('event_issue',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'issue_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createIndex('event_issue_event_id','event_issue','event_id');
		$this->createIndex('event_issue_issue_id','event_issue','issue_id');
		$this->addForeignKey('event_issue_event_id','event_issue','event_id','event','id');
		$this->addForeignKey('event_issue_issue_id','event_issue','issue_id','issue','id');

		$this->insert('issue',array('id'=>1,'name'=>'Operation requires scheduling'));

		foreach ($this->dbConnection->createCommand()->select('event_id')->from('element_operation')->where('status not in (1,3)')->queryAll() as $row) {
			$event = $this->dbConnection->createCommand()->select('id')->from('event')->where('id = '.$row['event_id'])->queryRow();
			$this->insert('event_issue',array('event_id'=>$event['id'],'issue_id'=>1));
		}
	}

	public function down()
	{
		$this->dropTable('event_issue');
		$this->dropTable('issue');
	}
}
