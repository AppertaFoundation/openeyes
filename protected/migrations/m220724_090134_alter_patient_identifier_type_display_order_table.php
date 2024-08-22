<?php

class m220724_090134_alter_patient_identifier_type_display_order_table extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('patient_identifier_type_display_order', 'auto_increment', 'boolean default false', true);
        $this->addOEColumn('patient_identifier_type_display_order', 'auto_increment_start', 'int unsigned not null', true);
    }

    public function down()
    {
        $this->dropOEColumn('patient_identifier_type_display_order', 'auto_increment_start', true);
        $this->dropOEColumn('patient_identifier_type_display_order', 'auto_increment', true);
    }
}
