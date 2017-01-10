<?php

class m170110_154312_rename_test_tables extends CDbMigration
{
	public function up()
	{
          
            $this->addColumn('et_ophingeneticresults_test', 'withdrawal_source_id', 'int(10) unsigned');
            $this->addForeignKey('et_ophingeneticresults_test_withdrawal_source_id', 'et_ophingeneticresults_test', 'withdrawal_source_id', 'et_ophindnaextraction_dnatests', 'id');

            $this->addColumn('et_ophingeneticresults_test', 'external_source_identifier', 'varchar(128)');

            $this->addColumn('et_ophingeneticresults_test', 'base_change_id', 'int(11)');
            $this->addForeignKey('et_ophingeneticresults_test_base_fk', 'et_ophingeneticresults_test', 'base_change_id', 'pedigree_base_change_type', 'id');

            $this->addColumn('et_ophingeneticresults_test', 'amino_acid_change_id', 'int(11)');
            $this->addForeignKey('et_ophingeneticresults_test_amino_acid_fk', 'et_ophingeneticresults_test', 'amino_acid_change_id', 'pedigree_amino_acid_change_type', 'id');

            $this->addColumn('et_ophingeneticresults_test', 'genomic_coordinate', 'varchar(5)');
            $this->addColumn('et_ophingeneticresults_test', 'genome_version', 'smallint unsigned');
            $this->addColumn('et_ophingeneticresults_test', 'gene_transcript', 'varchar(100)');
            
            $this->addColumn('et_ophingeneticresults_test_version', 'withdrawal_source_id', 'int(10) unsigned');
            $this->addForeignKey('et_ophingeneticresults_test_version_withdrawal_source_id', 'et_ophingeneticresults_test_version', 'withdrawal_source_id', 'et_ophindnaextraction_dnatests', 'id');
        
            $this->addColumn('et_ophingeneticresults_test_version', 'external_source_identifier', 'varchar(128)');
            
            $this->addColumn('et_ophingeneticresults_test_version', 'base_change_id', 'int(11)');
            $this->addForeignKey('et_ophingeneticresults_test_version_base_fk', 'et_ophingeneticresults_test_version', 'base_change_id', 'pedigree_base_change_type', 'id');
            
            $this->addColumn('et_ophingeneticresults_test_version', 'amino_acid_change_id', 'int(11)');
            $this->addForeignKey('et_ophingeneticresults_test_version_amino_acid_fk', 'et_ophingeneticresults_test_version', 'amino_acid_change_id', 'pedigree_amino_acid_change_type', 'id');
            
            $this->addColumn('et_ophingeneticresults_test_version', 'genomic_coordinate', 'varchar(5)');
            $this->addColumn('et_ophingeneticresults_test_version', 'genome_version', 'smallint unsigned');
            $this->addColumn('et_ophingeneticresults_test_version', 'gene_transcript', 'varchar(100)');
          
	}

	public function down()
	{
        
        $this->dropForeignKey('et_ophingeneticresults_test_withdrawal_source_id', 'et_ophingeneticresults_test');
        $this->dropColumn('et_ophingeneticresults_test', 'withdrawal_source_id');
        $this->dropColumn('et_ophingeneticresults_test', 'external_source_identifier');

        $this->dropColumn('et_ophingeneticresults_test', 'gene_transcript');
        $this->dropColumn('et_ophingeneticresults_test', 'genome_version');
        $this->dropColumn('et_ophingeneticresults_test', 'genomic_coordinate');
        $this->dropForeignKey('et_ophingeneticresults_test_amino_acid_fk', 'et_ophingeneticresults_test');
        $this->dropColumn('et_ophingeneticresults_test', 'amino_acid_change_id');
        $this->dropForeignKey('et_ophingeneticresults_test_base_fk', 'et_ophingeneticresults_test');
        $this->dropColumn('et_ophingeneticresults_test', 'base_change_id');


        $this->dropForeignKey('et_ophingeneticresults_test_version_withdrawal_source_id', 'et_ophingeneticresults_test_version');
        $this->dropColumn('et_ophingeneticresults_test_version', 'withdrawal_source_id');
        $this->dropColumn('et_ophingeneticresults_test_version', 'external_source_identifier');

        $this->dropColumn('et_ophingeneticresults_test_version', 'gene_transcript');
        $this->dropColumn('et_ophingeneticresults_test_version', 'genome_version');
        $this->dropColumn('et_ophingeneticresults_test_version', 'genomic_coordinate');

        $this->dropForeignKey('et_ophingeneticresults_test_version_amino_acid_fk', 'et_ophingeneticresults_test_version');
        $this->dropColumn('et_ophingeneticresults_test_version', 'amino_acid_change_id');
        $this->dropForeignKey('et_ophingeneticresults_test_version_base_fk', 'et_ophingeneticresults_test_version');
        $this->dropColumn('et_ophingeneticresults_test_version', 'base_change_id');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}