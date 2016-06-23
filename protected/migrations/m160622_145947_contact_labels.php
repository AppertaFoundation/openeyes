<?php

class m160622_145947_contact_labels extends OEMigration
{
	public function up()
	{
             $this->addColumn('contact_label', 'display', 'tinyint(1) unsigned not null default 1');
            $this->addColumn('contact_label_version', 'display', 'tinyint(1) unsigned not null default 1');
            
	}

	public function down()
	{
		 $this->dropColumn('contact_label', 'display');
            $this->dropColumn('contact_label_version', 'display');
	}


}