<?php

class m161228_144912_amino_base_change_types extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophingenetictest_test', 'base_change_id', 'int(11)');
        $this->addForeignKey('et_ophingenetictest_test_base_fk', 'et_ophingenetictest_test', 'base_change_id', 'pedigree_base_change_type', 'id');

        $this->addColumn('et_ophingenetictest_test', 'amino_acid_change_id', 'int(11)');
        $this->addForeignKey('et_ophingenetictest_test_amino_acid_fk', 'et_ophingenetictest_test', 'amino_acid_change_id', 'pedigree_amino_acid_change_type', 'id');

        $this->addColumn('et_ophingenetictest_test', 'genomic_coordinate', 'varchar(5)');
        $this->addColumn('et_ophingenetictest_test', 'genome_version', 'smallint unsigned');
        $this->addColumn('et_ophingenetictest_test', 'gene_transcript', 'varchar(100)');
    }

    public function down()
    {
        $this->dropColumn('et_ophingenetictest_test', 'gene_transcript');
        $this->dropColumn('et_ophingenetictest_test', 'genome_version');
        $this->dropColumn('et_ophingenetictest_test', 'genomic_coordinate');
        $this->dropForeignKey('et_ophingenetictest_test_amino_acid_fk', 'et_ophingenetictest_test');
        $this->dropColumn('et_ophingenetictest_test', 'amino_acid_change_id');
        $this->dropForeignKey('et_ophingenetictest_test_base_fk', 'et_ophingenetictest_test');
        $this->dropColumn('et_ophingenetictest_test', 'base_change_id');
    }
}
