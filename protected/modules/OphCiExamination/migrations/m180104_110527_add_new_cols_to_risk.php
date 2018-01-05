<?php

class m180104_110527_add_new_cols_to_risk extends OEMigration
{
	public function up()
	{
	    $this->addColumn('ophciexamination_risk', 'subspecialty_id', 'int(10) unsigned');
	    $this->addColumn('ophciexamination_risk', 'firm_id', 'int(10) unsigned');
	    $this->addColumn('ophciexamination_risk', 'episode_status_id', 'int(10) unsigned');
	    $this->addColumn('ophciexamination_risk', 'gender', 'varchar(1) NULL');
	    $this->addColumn('ophciexamination_risk', 'age_min', 'int(3) unsigned');
	    $this->addColumn('ophciexamination_risk', 'age_max', 'int(3) unsigned');

	    $this->addForeignKey('ophciexamination_risk_subspecialty', 'ophciexamination_risk', 'subspecialty_id', 'subspecialty', 'id');
	    $this->addForeignKey('ophciexamination_risk_firm', 'ophciexamination_risk', 'firm_id', 'firm', 'id');
	    $this->addForeignKey('ophciexamination_risk_episode_status', 'ophciexamination_risk', 'episode_status_id', 'episode_status', 'id');

	    $this->alterColumn("ophciexamination_risk", 'last_modified_user_id', 'int(10) unsigned NOT NULL AFTER age_max');
	    $this->alterColumn("ophciexamination_risk", 'last_modified_date', 'datetime NOT NULL AFTER last_modified_user_id');
	    $this->alterColumn("ophciexamination_risk", 'created_user_id', 'int(10) unsigned NOT NULL AFTER last_modified_date');
	    $this->alterColumn("ophciexamination_risk", 'created_date', 'datetime NOT NULL AFTER created_user_id');

	    $this->dropColumn("ophciexamination_risk", "required");
	}

	public function down()
	{
	    $this->dropForeignKey('ophciexamination_risk_subspecialty', 'ophciexamination_risk');
	    $this->dropForeignKey('ophciexamination_risk_firm', 'ophciexamination_risk');
	    $this->dropForeignKey('ophciexamination_risk_episode_status', 'ophciexamination_risk');
	    $this->dropForeignKey('ophciexamination_risk_episode_gender', 'ophciexamination_risk');

        $this->dropColumn('ophciexamination_risk', 'subspecialty_id');
        $this->dropColumn('ophciexamination_risk', 'firm_id');
        $this->dropColumn('ophciexamination_risk', 'episode_status_id');
        $this->dropColumn('ophciexamination_risk', 'gender_id');
        $this->dropColumn('ophciexamination_risk', 'age_min');
        $this->dropColumn('ophciexamination_risk', 'age_max');

        $this->addColumn("required", "tinyint(1) NOT NULL");
	}
}