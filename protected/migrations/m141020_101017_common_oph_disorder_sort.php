<?php

class m141020_101017_common_oph_disorder_sort extends CDbMigration
{
	public function up()
	{
		$this->addColumn('common_ophthalmic_disorder', 'display_order', 'int(10) unsigned DEFAULT 1');
		$this->addColumn('common_ophthalmic_disorder_version', 'display_order', 'int(10) unsigned DEFAULT 1');

		$current = $this->dbConnection->createCommand("SELECT common_ophthalmic_disorder.id, common_ophthalmic_disorder.subspecialty_id, disorder.term FROM common_ophthalmic_disorder "
			. "LEFT JOIN disorder ON common_ophthalmic_disorder.disorder_id = disorder.id order by common_ophthalmic_disorder.subspecialty_id, disorder.term")->queryAll();

		$subspecialty_id = null;
		$display_order = 1;
		foreach ($current as $c) {
			if ($c['subspecialty_id'] != $subspecialty_id) {
				$subspecialty_id = $c['subspecialty_id'];
				$display_order = 1;
			}
			$this->update('common_ophthalmic_disorder', array(
					'display_order' => $display_order++), 'id = :id', array(':id' => $c['id']));
		}

		$this->addColumn('secondaryto_common_oph_disorder', 'display_order', 'int(10) unsigned DEFAULT 1');
		$this->addColumn('secondaryto_common_oph_disorder_version', 'display_order', 'int(10) unsigned DEFAULT 1');
	}

	public function down()
	{
		$this->dropColumn('secondaryto_common_oph_disorder_version', 'display_order');
		$this->dropColumn('secondaryto_common_oph_disorder', 'display_order');
		$this->dropColumn('common_ophthalmic_disorder', 'display_order');
		$this->dropColumn('common_ophthalmic_disorder_version', 'display_order');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}