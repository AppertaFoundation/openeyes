<?php

class m210318_032556_create_referencetable_for_laser_admin_settings extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable('ophtrlaser_laserprocedure_institution', [
            'id' => 'pk',
            'laserprocedure_id' => 'int(10) unsigned NOT NULL',
            'institution_id' => 'int(10) unsigned NOT NULL',
            'CONSTRAINT `ophtrlaser_laserprocedure_lp_fk` FOREIGN KEY (`laserprocedure_id`) REFERENCES `ophtrlaser_laserprocedure` (`id`)',
            'CONSTRAINT `ophtrlaser_laserprocedure_institution_fk` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`)',
        ], true);
    }

    public function safeDown()
    {
        $this->dropOETable('ophtrlaser_laserprocedure_institution', true);
    }
}
