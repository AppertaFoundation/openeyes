<?php

class m230206_193720_modify_ophthalmic_disorder_tables_multitenancy extends OEMigration
{
    private $base_tables = array(
        'common_ophthalmic_disorder_group',
        'secondaryto_common_oph_disorder'
    );

    public function up()
    {
        foreach ($this->base_tables as $table_name) {
            $this->addOEColumn($table_name, 'subspecialty_id', 'int(10) unsigned NULL', true);

            $this->addForeignKey(
                $table_name . '_ss_fk',
                $table_name,
                'subspecialty_id',
                'subspecialty',
                'id'
            );
        }
    }

    public function down()
    {
        foreach ($this->base_tables as $table_name) {
            $this->dropOEColumn($table_name, 'subspecialty_id', true);
        }
    }
}
