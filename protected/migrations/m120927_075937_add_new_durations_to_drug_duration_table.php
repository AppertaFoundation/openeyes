<?php

class m120927_075937_add_new_durations_to_drug_duration_table extends CDbMigration
{
	public function up()
	{

		// Add missing create/edited columns on drug tables
		foreach (array('drug_duration','drug_allergy_assignment','drug_form','drug_frequency','drug_route','drug_type') as $table) {
			$this->addColumn($table, 'last_modified_user_id', 'int(10) unsigned NOT NULL DEFAULT 1');
			$this->addColumn($table, 'created_user_id', 'int(10) unsigned NOT NULL DEFAULT 1');
			$this->addColumn($table, 'last_modified_date', 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'');
			$this->addColumn($table, 'created_date', 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'');
			$this->addForeignKey($table.'_lmui_fk', $table, 'last_modified_user_id', 'user', 'id');
			$this->addForeignKey($table.'_cui_fk', $table, 'created_user_id', 'user', 'id');
		}

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
			} elseif (preg_match('/^([0-9]+) day/',$drug_duration->name,$m)) {
				$ids['days'][$m[1]] = $drug_duration->id;
			} elseif (preg_match('/^([0-9]+) week/',$drug_duration->name,$m)) {
				$ids['weeks'][$m[1]] = $drug_duration->id;
			} elseif (preg_match('/^([0-9]+) month/',$drug_duration->name,$m)) {
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
	}

	public function down()
	{
		$this->dropColumn('drug_duration','display_order');
	}
}
