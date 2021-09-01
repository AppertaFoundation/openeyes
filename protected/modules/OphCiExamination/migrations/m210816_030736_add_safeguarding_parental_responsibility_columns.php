<?php

class m210816_030736_add_safeguarding_parental_responsibility_columns extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->addOEColumn('et_ophciexamination_safeguarding', 'has_social_worker', 'tinyint(1) unsigned', true);
        $this->addOEColumn('et_ophciexamination_safeguarding', 'under_protection_plan', 'tinyint(1) unsigned', true);
        $this->addOEColumn('et_ophciexamination_safeguarding', 'accompanying_person_name', 'varchar(255)', true);
        $this->addOEColumn('et_ophciexamination_safeguarding', 'responsible_parent_name', 'varchar(255)', true);
    }

    public function safeDown()
    {
        $this->dropOEColumn('et_ophciexamination_safeguarding', 'has_social_worker', true);
        $this->dropOEColumn('et_ophciexamination_safeguarding', 'under_protection_plan', true);
        $this->dropOEColumn('et_ophciexamination_safeguarding', 'accompanying_person_name', true);
        $this->dropOEColumn('et_ophciexamination_safeguarding', 'responsible_parent_name', true);
    }
}
