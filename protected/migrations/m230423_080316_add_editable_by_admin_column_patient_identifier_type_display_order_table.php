<?php

class m230423_080316_add_editable_by_admin_column_patient_identifier_type_display_order_table extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('patient_identifier_type_display_order', 'only_editable_by_admin', 'boolean default false', true);
    }

    public function down()
    {
        $this->dropOEColumn('patient_identifier_type_display_order', 'only_editable_by_admin', true);
    }
}
