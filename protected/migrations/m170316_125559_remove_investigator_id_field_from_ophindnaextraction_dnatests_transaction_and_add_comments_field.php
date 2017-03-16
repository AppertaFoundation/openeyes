<?php

class m170316_125559_remove_investigator_id_field_from_ophindnaextraction_dnatests_transaction_and_add_comments_field extends CDbMigration
{
	public function up()
	{
        
        $this->addColumn('ophindnaextraction_dnatests_transaction','comments','varchar(255)');
        $this->dropColumn('ophindnaextraction_dnatests_transaction','investigator_id');
        
	    $this->dropForeignKey('ophindnaextraction_dnatests_transaction_sti_fk', 'ophindnaextraction_dnatests_transaction');
	    $this->alterColumn('ophindnaextraction_dnatests_transaction', 'study_id', 'INT(11) NOT NULL');
	    
	    $this->alterColumn('ophindnaextraction_dnatests_transaction', 'study_id', 'INT(11) NOT NULL');
	    
	    $this->addForeignKey(
	        'ophindnaextraction_dnatests_transaction_sti_fk', 
	        'ophindnaextraction_dnatests_transaction',
	        'study_id',
	        'genetics_study',
	        'id',
	        'RESTRICT',
	        'RESTRICT'
	    );
	    
	    
	    $this->addColumn('ophindnaextraction_dnatests_transaction_version','comments','varchar(255)');
        $this->dropColumn('ophindnaextraction_dnatests_transaction_version','investigator_id');
	}

	public function down()
	{
		$this->addColumn('ophindnaextraction_dnatests_transaction','investigator_id','int(10) unsigned NOT NULL');
		$this->dropColumn('ophindnaextraction_dnatests_transaction','comments');
		
	    $this->dropForeignKey('ophindnaextraction_dnatests_transaction_sti_fk', 'ophindnaextraction_dnatests_transaction');
	    $this->alterColumn('ophindnaextraction_dnatests_transaction', 'study_id', 'INT(10) UNSIGNED NOT NULL');
	    
	    $this->alterColumn('ophindnaextraction_dnatests_transaction', 'study_id', 'INT(10) UNSIGNED NOT NULL');
	    $this->addForeignKey(
	        'ophindnaextraction_dnatests_transaction_sti_fk', 
	        'ophindnaextraction_dnatests_transaction',
	        'study_id',
	        'ophindnaextraction_dnatests_study',
	        'id',
	        'RESTRICT',
	        'RESTRICT'
	    );		
	    
		
		$this->addColumn('ophindnaextraction_dnatests_transaction_version','investigator_id','int(10) unsigned NOT NULL');
	    $this->dropColumn('ophindnaextraction_dnatests_transaction_version','comments');
	}

}