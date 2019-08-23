<?php

class m190822_092917_add_dilation_drug_active extends CDbMigration
{
	public function up()
  {
    $this->addColumn('ophciexamination_dilation_drugs', 'is_active', "TINYINT DEFAULT 1");
    $this->addColumn('ophciexamination_dilation_drugs_version', 'is_active', "TINYINT DEFAULT 1");
	}

	public function down()
  {
    $this->dropColumn('ophciexamination_dilation_drugs', 'is_active');
    $this->dropColumn('ophciexamination_dilation_drugs_version', 'is_active');
	}
}
