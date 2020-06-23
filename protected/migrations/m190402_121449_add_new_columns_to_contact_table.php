<?php

class m190402_121449_add_new_columns_to_contact_table extends CDbMigration
{
    public function up()
    {
        $this->addColumn('contact', 'active', 'TINYINT(1) DEFAULT 1');
        $this->addColumn('patient_contact_assignment', 'comment', 'text');
        $this->addColumn('contact', 'national_code', 'VARCHAR(25)');
        $this->addColumn('contact', 'fax', 'VARCHAR(25)');
        $this->addColumn('contact_version', 'fax', 'VARCHAR(25)');
        $this->addColumn('contact_version', 'active', 'TINYINT(1) DEFAULT 1');
        $this->addColumn('contact_version', 'national_code', 'VARCHAR(25)');
        $this->addColumn('patient_contact_assignment_version', 'comment', 'text');
    }

    public function down()
    {
        $this->dropColumn('contact', 'active');
        $this->dropColumn('patient_contact_assignment', 'comment');
        $this->dropColumn('contact', 'national_code');
        $this->dropColumn('contact', 'fax');
        $this->dropColumn('contact_version', 'active');
        $this->dropColumn('contact_version', 'comment');
        $this->dropColumn('patient_contact_assignment_version', 'national_code');
        $this->dropColumn('contact_version', 'fax');
    }
}
