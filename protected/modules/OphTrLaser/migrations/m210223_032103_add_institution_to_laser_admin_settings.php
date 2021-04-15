<?php

class m210223_032103_add_institution_to_laser_admin_settings extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn('ophtrlaser_site_laser', 'institution_id', 'int(10) unsigned AFTER display_order', true);
        $this->addForeignKey('ophtrlaser_site_laser_institution_fk', 'ophtrlaser_site_laser', 'institution_id',
            'institution', 'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('ophtrlaser_site_laser_institution_fk', 'ophtrlaser_site_laser');
        $this->dropOEColumn('ophtrlaser_site_laser', 'institution_id', true);
    }
}
