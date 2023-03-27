<?php

class m230313_174712_add_example_column_to_patient_identifier_type extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('patient_identifier_type', 'validation_example', 'varchar(255) default null AFTER pad', true);
    }

    public function down()
    {
        $this->dropOEColumn('patient_identifier_type', 'validation_example', true);
    }
}
