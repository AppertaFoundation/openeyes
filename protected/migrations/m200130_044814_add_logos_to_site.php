<?php

class m200130_044814_add_logos_to_site extends OEMigration
{
	public function safeUp()
	{
        // Creating Table
        $this->createOETable('site_logo', array(
            'id' => 'pk',
            'primary_logo' => 'mediumblob',
            'secondary_logo' => 'mediumblob'
        ));

        // Adding column to sites to save foreign key relationship
        $this->addColumn('site', 'logo_id', 'integer');

        // Adding foreign key to sites
        $this->addForeignKey('site_logo_id_fk', 'site', 'logo_id', 'site_logo', 'id');

        // Adding Default logo -- currently null but this could pull the current logo
        $this->insert('site_logo',   array(
            'primary_logo' => /* TODO get current primary_logo from application server*/ null, 
        'secondary_logo' => /* TODO get current secondary_logo from application server*/ null));

	}

	public function safeDown()
	{
        // Dropping foreign key from sites
        $this->dropForeignKey('site_logo_id_fk', 'site');

        // Dropping column from sites that save foreign key relationship
        $this->dropColumn('site', 'logo_id');
        
        // Dropping Table
        $this->dropOETable('site_logo');
	}
}