<?php

class m200106_010919_add_email_column_to_firm_table extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn('firm', 'service_email', 'varchar(255) AFTER name', true);
        $this->addOEColumn('firm', 'context_email', 'varchar(255) AFTER service_email', true);
    }

    public function safeDown()
    {
        // remove the email columns from the firm table
        $this->dropOEColumn('firm', 'service_email', true);
        $this->dropOEColumn('firm', 'context_email', true);
    }
}
