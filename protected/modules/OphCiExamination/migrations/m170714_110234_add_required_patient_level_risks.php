<?php

class m170714_110234_add_required_patient_level_risks extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophciexamination_risk', 'required', 'boolean NOT NULL default false');
        $this->addColumn('ophciexamination_risk_version', 'required', 'boolean NOT NULL default false');
        foreach (array('Anticoagulants', 'Alpha blockers') as $name) {
            $this->update('ophciexamination_risk', array('required' => true), 'name = :name', array(':name' => $name));
        }
    }

    public function down()
    {
        $this->dropColumn('ophciexamination_risk_version', 'required');
        $this->dropColumn('ophciexamination_risk', 'required');
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