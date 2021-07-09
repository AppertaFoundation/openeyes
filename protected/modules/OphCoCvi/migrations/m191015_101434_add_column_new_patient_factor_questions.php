<?php

class m191015_101434_add_column_new_patient_factor_questions extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophcocvi_clericinfo', 'preferred_comm','VARCHAR(255) DEFAULT NULL');
        $this->addColumn('et_ophcocvi_clericinfo_version', 'preferred_comm','VARCHAR(255) DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('et_ophcocvi_clericinfo', 'preferred_comm');
        $this->dropColumn('et_ophcocvi_clericinfo_version', 'preferred_comm');
    }
}