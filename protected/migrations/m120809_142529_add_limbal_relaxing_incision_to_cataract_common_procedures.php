<?php

class m120809_142529_add_limbal_relaxing_incision_to_cataract_common_procedures extends CDbMigration
{
	public function up()
	{
		$db = $this->getDbConnection();
		$proc = $db->createCommand("select id from proc where snomed_code= :snomed_code;")
			->bindValues(array( ':snomed_code' => '397313004'))->queryScalar();

		$subspecialty = $db->createCommand("select id from subspecialty where ref_spec= :ref_spec;")
		->bindValues(array( ':ref_spec' => 'CA'))->queryScalar();

		if (!$proc ) {
			$this->insert('proc',
				array(
					'term'=> 'Limbal relaxing incision',
					'short_format'=>'Limbal Rel Incn',
					'default_duration'=> 10,
					'snomed_code' => '397313004',
					'snomed_term' => 'Limbal relaxing incision'
				)
			);
			/*$proc = new Procedure;
			$proc->term = 'Limbal relaxing incision';
			$proc->short_format = 'Limbal Rel Incn';
			$proc->default_duration = 10;
			$proc->snomed_code = '397313004';
			$proc->snomed_term = 'Limbal relaxing incision';
			$proc->save();*/
		}
		if ($subspecialty) {
			$this->insert('proc_subspecialty_assignment',array('proc_id'=>$proc['id'],'subspecialty_id'=>$subspecialty['id']));
		}
	}

	public function down()
	{
		$db = $this->getDbConnection();
		$proc = $db->createCommand("select id from proc where snomed_code= :snomed_code;")
			->bindValues(array( ':snomed_code' => '397313004'))->queryScalar();

		$subspecialty = $db->createCommand("select id from subspecialty where ref_spec= :ref_spec;")
			->bindValues(array( ':ref_spec' => 'CA'))->queryScalar();

		if ($subspecialty ) {
			$this->delete('proc_subspecialty_assignment','proc_id='.$proc['id'] .' and subspecialty_id='. $subspecialty['id']);
		}
	}
}
