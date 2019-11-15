<?php

class m190214_102307_automatic_medication_sets extends OEMigration
{
	public function up()
	{
		$this->addColumn('medication_set', 'automatic', 'BOOLEAN NOT NULL DEFAULT 0');
		$this->addColumn('medication_set_version', 'automatic', 'BOOLEAN NOT NULL DEFAULT 0');

		$this->createOETable('medication_set_auto_rule_attribute', array(
			'id' => 'pk',
			'medication_set_id' => 'INT(11) NOT NULL',
			'medication_attribute_option_id' => 'INT(11) NOT NULL'
		), true);

		$this->addForeignKey('fk_msara_msid', 'medication_set_auto_rule_attribute', 'medication_set_id', 'medication_set', 'id');
		$this->addForeignKey('fk_msara_mattroptid', 'medication_set_auto_rule_attribute', 'medication_attribute_option_id', 'medication_attribute_option', 'id');

		$this->createOETable('medication_set_auto_rule_set_membership', array(
			'id' => 'pk',
			'target_medication_set_id' => 'INT(11) NOT NULL',
			'source_medication_set_id' => 'INT(11) NOT NULL'
		), true);

		$this->addForeignKey('fk_msarsm_tmsid', 'medication_set_auto_rule_set_membership', 'target_medication_set_id', 'medication_set', 'id');
		$this->addForeignKey('fk_msarsm_smsid', 'medication_set_auto_rule_set_membership', 'source_medication_set_id', 'medication_set', 'id');

		$this->createOETable('medication_set_auto_rule_medication', array(
			'id' => 'pk',
			'medication_set_id' => 'INT(11) NOT NULL',
			'medication_id' => 'INT(11) NOT NULL',
			'include_parent' => 'TINYINT DEFAULT 0 NOT NULL',
			'include_children' => 'TINYINT DEFAULT 0 NOT NULL'
		), true);

		$this->addForeignKey('fk_msarm_msid', 'medication_set_auto_rule_medication', 'medication_set_id', 'medication_set', 'id');
		$this->addForeignKey('fk_msarm_mid', 'medication_set_auto_rule_medication', 'medication_id', 'medication', 'id');

        $command = Yii::app()->db;
        $glaucoma_tag = $command->createCommand('SELECT id, name FROM tag WHERE name = "Glaucoma"')->queryRow();

        if ($glaucoma_tag) {
            // creating Glaucoma set
            $command->createCommand("INSERT INTO medication_set(name, automatic) values ('{$glaucoma_tag['name']}', 1)")->execute();
            $medication_set_id = $command->getLastInsertID();

            // get the id of the OEScape usage code
            $oescape_usage_code_id = $command->createCommand()
                ->select('id')
                ->from('medication_usage_code')
                ->where('usage_code = :usage_code', [':usage_code' => 'OEScape'])
                ->queryScalar();

            // get Glaucoma subspecialty's Id
            $gl_subspecialty_id = $command->createCommand()
                ->select('id')
                ->from('subspecialty')
                ->where('ref_spec = :ref_spec', [':ref_spec' => 'GL'])
                ->queryScalar();

            // add rule to OEScape Glaucoma
            $command->createCommand("INSERT INTO medication_set_rule (medication_set_id, subspecialty_id, usage_code_id)
											VALUES ($medication_set_id, $gl_subspecialty_id, $oescape_usage_code_id )")->execute();

            // get all the items belong to Glaucoma
            $drug_tags = $command
                ->createCommand('SELECT drug_id FROM drug_tag WHERE tag_id = ' . $glaucoma_tag['id'])
                ->queryAll();

            if ($drug_tags) {
                foreach ($drug_tags as $drug_tag) {
                    $ref_medication_id = $command
                        ->createCommand("SELECT id FROM medication WHERE source_old_id = '" . $drug_tag['drug_id'] . "' AND source_subtype = 'drug'")
                        ->queryScalar();

                    if ($ref_medication_id) {
                        $command->createCommand("INSERT INTO medication_set_item(medication_id, medication_set_id) values (" . $ref_medication_id . ", " . $medication_set_id . " )")->execute();

                        $q = "INSERT INTO medication_set_auto_rule_medication (medication_set_id, medication_id, include_children, include_parent) values ({$medication_set_id}, {$ref_medication_id}, 1, 1)";
                        $command->createCommand($q)->execute();
                    }
                }
            }
        }
	}

	public function down()
	{
		$this->dropOETable('medication_set_auto_rule_medication', true);
		$this->dropOETable('medication_set_auto_rule_set_membership', true);
		$this->dropOETable('medication_set_auto_rule_attribute', true);
	}
}