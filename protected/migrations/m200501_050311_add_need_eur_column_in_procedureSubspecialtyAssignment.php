<?php

class m200501_050311_add_need_eur_column_in_procedureSubspecialtyAssignment extends OEMigration
{
    public function up()
    {
        // adding new column 'need_eur' to proc_subspecialty_assignment
        $this->addOEColumn('proc_subspecialty_assignment', 'need_eur', 'tinyint default 0', true);
    }

    public function down()
    {
        $this->dropOEColumn('proc_subspecialty_assignment', 'need_eur');
    }
}
