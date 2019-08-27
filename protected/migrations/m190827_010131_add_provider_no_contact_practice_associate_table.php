<?php

class m190827_010131_add_provider_no_contact_practice_associate_table extends CDbMigration
{
	public function up()
	{
        $this->addColumn('contact_practice_associate' , 'provider_no' , 'varchar(255) DEFAULT NULL AFTER practice_id');
	}

	public function down()
	{
        $this->dropColumn('contact_practice_associate', 'provider_no');
	}
}