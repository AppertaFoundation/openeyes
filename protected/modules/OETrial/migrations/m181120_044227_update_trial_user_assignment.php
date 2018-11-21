<?php

class m181120_044227_update_trial_user_assignment extends OEMigration
{
  // Use safeUp/safeDown to do migration with transaction
  public function safeUp()
  {
  	$this->dropForeignKey('principalUser','trial');
    $this->dropColumn('trial', 'principle_investigator_user_id');
		$this->dropForeignKey('coordinatorUser','trial');
		$this->dropColumn('trial', 'coordinator_user_id');

    $this->addColumn('user_trial_assignment', 'is_principal_investigator', 'boolean NOT NULL default false');
    $this->addColumn('user_trial_assignment', 'is_study_coordinator', 'boolean NOT NULL default false');

    $this->addColumn('user_trial_assignment_version', 'is_principal_investigator', 'boolean NOT NULL default false');
    $this->addColumn('user_trial_assignment_version', 'is_study_coordinator', 'boolean NOT NULL default false');
  }

  public function safeDown()
  {
    $this->addColumn('trial','principle_investigator_user_id', 'int(10) unsigned NOT NULL');
    $this->addColumn('trial','coordinator_user_id', 'int(10) unsigned NOT NULL');


    $this->dropColumn('user_trial_assignment','is_principal_investigator');
    $this->dropColumn('user_trial_assignment','is_study_coordinator');

    $this->dropColumn('user_trial_assignment_version','is_principal_investigator');
    $this->dropColumn('user_trial_assignment_version','is_study_coordinator');
  }
}