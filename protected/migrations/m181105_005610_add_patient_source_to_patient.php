<?php

class m181105_005610_add_patient_source_to_patient extends OEMigration
{
  // Use safeUp/safeDown to do migration with transaction
  public function safeUp()
  {
        $this->addColumn('patient', 'patient_source', 'tinyint(1) unsigned NOT NULL DEFAULT 0');
        $this->addColumn('patient_version', 'patient_source', 'tinyint(1) unsigned NOT NULL DEFAULT 0');
  }

  public function safeDown()
  {
        $this->dropColumn('patient', 'patient_source');
        $this->dropColumn('patient_version', 'patient_source');
  }

}