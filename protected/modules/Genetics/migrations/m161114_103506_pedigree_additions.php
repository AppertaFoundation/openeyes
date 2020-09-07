<?php

class m161114_103506_pedigree_additions extends OEMigration
{
    public function up()
    {
        $this->createOETable('pedigree_base_change_type', array(
            'id' => 'pk',
            'change' => 'varchar(50)',
        ));
        $this->addColumn('pedigree', 'base_change_id', 'int(11)');
        $this->addForeignKey('pedigree_base_fk', 'pedigree', 'base_change_id', 'pedigree_base_change_type', 'id');

        $this->createOETable('pedigree_amino_acid_change_type', array(
            'id' => 'pk',
            'change' => 'varchar(50)',
        ));
        $this->addColumn('pedigree', 'amino_acid_change_id', 'int(11)');
        $this->addForeignKey('pedigree_amino_acid_fk', 'pedigree', 'amino_acid_change_id', 'pedigree_amino_acid_change_type', 'id');

        $this->addColumn('pedigree', 'genomic_coordinate', 'varchar(5)');
        $this->addColumn('pedigree', 'genome_version', 'smallint unsigned');
        $this->addColumn('pedigree', 'gene_transcript', 'varchar(100)');

        $this->versionExistingTable('pedigree');
    }

    public function down()
    {
        $this->dropTable('pedigree_version');
        $this->dropColumn('pedigree', 'gene_transcript');
        $this->dropColumn('pedigree', 'genome_version');
        $this->dropColumn('pedigree', 'genomic_coordinate');
        $this->dropForeignKey('pedigree_amino_acid_fk', 'pedigree');
        $this->dropColumn('pedigree', 'amino_acid_change_id');
        $this->dropOETable('pedigree_amino_acid_change_type');
        $this->dropForeignKey('pedigree_base_fk', 'pedigree');
        $this->dropColumn('pedigree', 'base_change_id');
        $this->dropOETable('pedigree_base_change_type');
    }
}
