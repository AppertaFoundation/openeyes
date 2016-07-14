<?php

class m160712_081921_new_fields_anterior_segment extends OEMigration
{
	public function up()
	{
            $this->addColumn('et_ophciexamination_anteriorsegment', 'guttata', 'tinyint(1) unsigned not null');
            $this->addColumn('et_ophciexamination_anteriorsegment', 'pseudoexfoliation', 'tinyint(1) unsigned not null');
            $this->addColumn('et_ophciexamination_anteriorsegment', 'red_reflex', 'tinyint(1) unsigned not null');
            $this->addColumn('et_ophciexamination_anteriorsegment', 'stable_lens', 'tinyint(1) unsigned not null');
            $this->addColumn('et_ophciexamination_anteriorsegment', 'state_dilation', 'tinyint(1) unsigned not null');
            
            $this->addColumn('et_ophciexamination_anteriorsegment_version', 'guttata', 'tinyint(1) unsigned not null');
            $this->addColumn('et_ophciexamination_anteriorsegment_version', 'pseudoexfoliation', 'tinyint(1) unsigned not null');
            $this->addColumn('et_ophciexamination_anteriorsegment_version', 'red_reflex', 'tinyint(1) unsigned not null');
            $this->addColumn('et_ophciexamination_anteriorsegment_version', 'stable_lens', 'tinyint(1) unsigned not null');
            $this->addColumn('et_ophciexamination_anteriorsegment_version', 'state_dilation', 'tinyint(1) unsigned not null');
	}

	public function down()
	{
            $this->dropColumn('et_ophciexamination_anteriorsegment', 'guttata');
            $this->dropColumn('et_ophciexamination_anteriorsegment', 'pseudoexfoliation');
            $this->dropColumn('et_ophciexamination_anteriorsegment', 'red_reflex');
            $this->dropColumn('et_ophciexamination_anteriorsegment', 'stable_lens');
            $this->dropColumn('et_ophciexamination_anteriorsegment', 'state_dilation');
            
            $this->dropColumn('et_ophciexamination_anteriorsegment_version', 'guttata');
            $this->dropColumn('et_ophciexamination_anteriorsegment_version', 'pseudoexfoliation');
            $this->dropColumn('et_ophciexamination_anteriorsegment_version', 'red_reflex');
            $this->dropColumn('et_ophciexamination_anteriorsegment_version', 'stable_lens');
            $this->dropColumn('et_ophciexamination_anteriorsegment_version', 'state_dilation');
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