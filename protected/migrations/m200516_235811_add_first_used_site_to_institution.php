<?php

class m200516_235811_add_first_used_site_to_institution extends OEMigration
{
	public function up()
    {
        $this->addOEColumn('institution', 'first_used_site_id', 'INT(10) UNSIGNED NULL DEFAULT NULL', true);
        $this->addForeignKey('fk_institution_first_site',
            'institution',
            'first_used_site_id',
            'site',
            'id'
        );
	}

	public function down()
	{
        $this->dropForeignKey('fk_institution_first_site', 'institution');
        $this->dropOEColumn('institution', 'first_used_site_id');
	}
}
