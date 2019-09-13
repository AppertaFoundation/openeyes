<?php

class m190912_091418_migrate_tags_to_medication_sets extends OEMigration
{
	public function up()
	{
		$this->execute('UPDATE ophciexamination_risk_tag t
                         JOIN tag tg ON tg.id = t.`tag_id`
                         SET medication_set_id = (SELECT id FROM medication_set mst WHERE mst.name = tg.name)'
		);

		$this->dropForeignKey('ophciexamination_risk_tag_tag_fk', 'ophciexamination_risk_tag');
		$this->dropColumn('ophciexamination_risk_tag', 'tag_id');
		$this->addColumn('ophciexamination_risk_tag', 'id' , 'pk');

		$this->dropForeignKey('drug_tags_ti_fk', 'drug_tag');
		$this->dropForeignKey('drug_type_tags_fk', 'drug_type');
		$this->dropForeignKey('medication_tags_ti_fk', 'medication_drug_tag');

		$this->dropColumn('drug_type', 'tag_id');
		$this->dropColumn('drug_type_version', 'tag_id');
		$this->dropColumn('medication_drug_tag', 'tag_id');
		$this->dropColumn('medication_drug_tag_version', 'tag_id');
		$this->dropOETable('drug_type', true);
		$this->dropOETable('medication_drug_tag', true);
		$this->dropOETable('drug_tag', true);
		$this->dropOETable('tag', true);
	}

	public function down()
	{
		echo "m190912_091418_migrate_tags_to_medication_sets does not support migration down.\n";
		return false;
	}
}