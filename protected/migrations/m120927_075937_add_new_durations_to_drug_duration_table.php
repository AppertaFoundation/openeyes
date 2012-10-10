<?php

class m120927_075937_add_new_durations_to_drug_duration_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('drug_duration','display_order','int(10) unsigned NOT NULL DEFAULT 1');

		foreach (array('24 hours','48 hours','1 day','3 days','4 days','6 weeks') as $name) {
			if (!DrugDuration::model()->find('name=?',array($name))) {
				$dd = new DrugDuration;
				$dd->name = $name;
				$dd->save();
			}
		}

		$ids = array(
			'hours' => array(),
			'days' => array(),
			'weeks' => array(),
			'months' => array(),
			'other' => array(),
		);

		foreach (DrugDuration::model()->findAll() as $drug_duration) {
			if (preg_match('/^([0-9]+) hour/',$drug_duration->name,$m)) {
				$ids['hours'][$m[1]] = $drug_duration->id;
			} else if (preg_match('/^([0-9]+) day/',$drug_duration->name,$m)) {
				$ids['days'][$m[1]] = $drug_duration->id;
			} else if (preg_match('/^([0-9]+) week/',$drug_duration->name,$m)) {
				$ids['weeks'][$m[1]] = $drug_duration->id;
			} else if (preg_match('/^([0-9]+) month/',$drug_duration->name,$m)) {
				$ids['months'][$m[1]] = $drug_duration->id;
			} else {
				$ids['other'][] = $drug_duration->id;
			}
		}

		ksort($ids['hours']);
		ksort($ids['days']);
		ksort($ids['weeks']);
		ksort($ids['months']);
		ksort($ids['other']);

		$i = 1;
		foreach ($ids as $list => $items) {
			foreach ($items as $item) {
				$this->update('drug_duration',array('display_order'=>$i),'id='.$item);
				$i++;
			}
		}

		$this->update('drug_duration',array('display_order'=>6),'id=1');
		$this->update('drug_duration',array('display_order'=>7),'id=2');
		$this->update('drug_duration',array('display_order'=>8),'id=3');
		$this->update('drug_duration',array('display_order'=>9),'id=4');
		$this->update('drug_duration',array('display_order'=>10),'id=5');

		$this->insert('drug_duration',array('id'=>6,'name'=>'24 hours','display_order'=>1));
		$this->insert('drug_duration',array('id'=>7,'name'=>'48 hours','display_order'=>2));
		$this->insert('drug_duration',array('id'=>8,'name'=>'1 day','display_order'=>3));
		$this->insert('drug_duration',array('id'=>9,'name'=>'3 days','display_order'=>4));
		$this->insert('drug_duration',array('id'=>10,'name'=>'4 days','display_order'=>5));
		$this->insert('drug_duration',array('id'=>11,'name'=>'6 weeks','display_order'=>11));
		$this->insert('drug_duration',array('id'=>12,'name'=>'Other','display_order'=>12));
	}

	public function down()
	{
		$this->dropColumn('drug_duration','display_order');
	}
}
