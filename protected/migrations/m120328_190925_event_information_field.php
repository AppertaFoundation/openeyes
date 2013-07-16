<?php

class m120328_190925_event_information_field extends CDbMigration
{
	public function up()
	{
		$this->addColumn('event','info','varchar(1024) COLLATE utf8_bin DEFAULT NULL');

		foreach ($this->dbConnection->createCommand()->select('id, event_id')->from('element_operation')->where('status not in (1,3)')->queryAll() as $row) {
			$event = $this->dbConnection->createCommand()->select('id')->from('event')->where('id = '.$row['event_id'])->queryRow();

			$info = '';

			foreach ($this->dbConnection->createCommand()->select('proc_id')->from('operation_procedure_assignment')->where('operation_id='.$row['id'])->order('display_order asc')->queryAll() as $opa) {

				$proc = $this->dbConnection->createCommand()->select('term')->from('proc')->where('id='.$opa['proc_id'])->queryRow();

				if ($info) {
					$info .= "\n";
				}

				$info .= $proc['term'];
			}

			$this->update('event',array('info'=>$info),'id='.$event['id']);
		}
	}

	public function down()
	{
		$this->dropColumn('event','info');
	}
}
