<?php

class m170503_151949_add_to_location_to_internalreferral extends CDbMigration
{
	public function up()
	{
	    //fix previous site_id type
        $this->alterColumn('et_ophcocorrespondence_letter', 'site_id', 'INT(10) UNSIGNED NULL');
        $this->alterColumn('et_ophcocorrespondence_letter_version', 'site_id', 'INT(10) UNSIGNED NULL');
        $this->addForeignKey("et_ophcocorrespondence_letter_ibfk_4", 'et_ophcocorrespondence_letter', 'site_id', 'site', 'id');

	    $this->addColumn('et_ophcocorrespondence_letter', 'to_site_id', 'INT(10) UNSIGNED NULL');
	    $this->addColumn('et_ophcocorrespondence_letter_version', 'to_site_id', 'INT(10) UNSIGNED NULL');
        $this->addForeignKey("et_ophcocorrespondence_letter_ibfk_5", 'et_ophcocorrespondence_letter', 'to_site_id', 'site', 'id');

        //rename to_consultant_id to to_firm_id
        $this->dropForeignKey('et_ophcocorrespondence_letter_ibfk_2', 'et_ophcocorrespondence_letter');
        $this->renameColumn('et_ophcocorrespondence_letter', 'to_consultant_id', 'to_firm_id');
        $this->renameColumn('et_ophcocorrespondence_letter_version', 'to_consultant_id', 'to_firm_id');

        //in case the column wasn't empty
        $this->dbConnection->createCommand('UPDATE et_ophcocorrespondence_letter SET to_firm_id = NULL')->execute();


        $this->addForeignKey("et_ophcocorrespondence_letter_ibfk_6", 'et_ophcocorrespondence_letter', 'to_firm_id', 'firm', 'id');

	}

	public function down()
	{
	    $this->dropForeignKey('et_ophcocorrespondence_letter_ibfk_4', 'et_ophcocorrespondence_letter');

	    $this->dropForeignKey('et_ophcocorrespondence_letter_ibfk_5', 'et_ophcocorrespondence_letter');
	    $this->dropColumn('et_ophcocorrespondence_letter', 'to_site_id');
	    $this->dropColumn('et_ophcocorrespondence_letter_version', 'to_site_id');

        $this->dropForeignKey('et_ophcocorrespondence_letter_ibfk_6', 'et_ophcocorrespondence_letter');
        $this->renameColumn('et_ophcocorrespondence_letter', 'to_firm_id', 'to_consultant_id');
        $this->renameColumn('et_ophcocorrespondence_letter_version', 'to_firm_id', 'to_consultant_id');

        $this->addForeignKey('et_ophcocorrespondence_letter_ibfk_2', 'et_ophcocorrespondence_letter', 'to_consultant_id', 'user', 'id');
	}
}