<?php

class m120809_142529_add_limbal_relaxing_incision_to_cataract_common_procedures extends CDbMigration
{
	public function up()
	{
		if (!$proc = Procedure::model()->find('snomed_code=?',array('397313004'))) {
			$proc = new Procedure;
			$proc->term = 'Limbal relaxing incision';
			$proc->short_format = 'Limbal Rel Incn';
			$proc->default_duration = 10;
			$proc->snomed_code = '397313004';
			$proc->snomed_term = 'Limbal relaxing incision';
			$proc->save();
		}
		if ($subspecialty = Subspecialty::model()->find('ref_spec=?',array('CA'))) {
			$this->insert('proc_subspecialty_assignment',array('proc_id'=>$proc->id,'subspecialty_id'=>$subspecialty->id));
		}
	}

	public function down()
	{
		$proc = Procedure::model()->find('snomed_code=?',array('397313004'));

		if ($subspecialty = Subspecialty::model()->find('ref_spec=?',array('CA'))) {
			$this->delete('proc_subspecialty_assignment','proc_id='.$proc->id.' and subspecialty_id='.$subspecialty->id);
		}
	}
}
