<?php

class m191015_101434_add_column_new_patient_factor_questions extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('et_ophcocvi_clericinfo', 'preferred_comm', 'VARCHAR(255) DEFAULT NULL', true);
    }

    public function down()
    {
        $this->dropOEColumn('et_ophcocvi_clericinfo', 'preferred_comm', true);
    }
}
