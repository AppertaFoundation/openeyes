<?php

class m181120_044227_update_trial_user_assignment extends OEMigration
{
  // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->dropForeignKey('trial_principle_investigator_user_id_fk', 'trial');
        $this->dropColumn('trial', 'principle_investigator_user_id');
        $this->dropForeignKey('trial_coordinator_user_id_fk', 'trial');
        $this->dropColumn('trial', 'coordinator_user_id');

        $this->dropColumn('trial_version', 'principle_investigator_user_id');
        $this->dropColumn('trial_version', 'coordinator_user_id');

        $this->addColumn('user_trial_assignment', 'is_principal_investigator', 'boolean NOT NULL default false');
        $this->addColumn('user_trial_assignment', 'is_study_coordinator', 'boolean NOT NULL default false');

        $this->addColumn('user_trial_assignment_version', 'is_principal_investigator', 'boolean NOT NULL default false');
        $this->addColumn('user_trial_assignment_version', 'is_study_coordinator', 'boolean NOT NULL default false');
    }

    public function safeDown()
    {
        $this->addColumn('trial', 'principle_investigator_user_id', 'int(10) unsigned NOT NULL default 1');
        $this->addForeignKey('trial_principle_investigator_user_id_fk', 'trial', 'principle_investigator_user_id', 'user', 'id');
        $this->addColumn('trial', 'coordinator_user_id', 'int(10) unsigned NOT NULL default 1');
        $this->addForeignKey('trial_coordinator_user_id_fk', 'trial', 'coordinator_user_id', 'user', 'id');


        $this->addColumn('trial_version', 'principle_investigator_user_id', 'int(10) unsigned NOT NULL default 1');
        $this->addColumn('trial_version', 'coordinator_user_id', 'int(10) unsigned NOT NULL default 1');

        $this->dropColumn('user_trial_assignment', 'is_principal_investigator');
        $this->dropColumn('user_trial_assignment', 'is_study_coordinator');

        $this->dropColumn('user_trial_assignment_version', 'is_principal_investigator');
        $this->dropColumn('user_trial_assignment_version', 'is_study_coordinator');
    }
}