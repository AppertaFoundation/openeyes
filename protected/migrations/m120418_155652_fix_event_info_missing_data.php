<?php

class m120418_155652_fix_event_info_missing_data extends CDbMigration
{
	public function up()
	{
		foreach ($this->dbConnection->createCommand()->select('id, event_id')->from('element_operation')->queryAll() as $row) {
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
	}
}
