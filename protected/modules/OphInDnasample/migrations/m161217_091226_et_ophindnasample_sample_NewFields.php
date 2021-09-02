<?php

class m161217_091226_et_ophindnasample_sample_NewFields extends CDbMigration
{
    /*
    public function up()
    {
    }

    public function down()
    {
        echo "m161217_091226_ophindnasample_sample_NewFields does not support migration down.\n";
        return false;
    }
*/

    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // new columns

        $this->addColumn('et_ophindnasample_sample', 'other_sample_type', 'string');
        $this->addColumn('et_ophindnasample_sample', 'consented_by', 'integer');
        $this->addColumn('et_ophindnasample_sample', 'is_local', 'boolean');
        $this->addColumn('et_ophindnasample_sample', 'destination', 'text');

        // pivot table with foreign keys

        $this->createTable('et_ophindnasample_sample_genetics_studies', array(
            'id'=>'pk',
            'et_ophindnasample_sample_id'=>'INT(10) unsigned',
            'genetics_study_id'=>'integer'
        ), '');

        $this->createIndex(
            'idx_et_ophindnasample_sample_id',
            'et_ophindnasample_sample_genetics_studies',
            'et_ophindnasample_sample_id'
        );

        $this->createIndex(
            'idx_genetics_study_id',
            'et_ophindnasample_sample_genetics_studies',
            'genetics_study_id'
        );

        $this->addForeignKey(
            'fk_et_ophindnasample_sample_id',
            'et_ophindnasample_sample_genetics_studies',
            'et_ophindnasample_sample_id',
            'et_ophindnasample_sample',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_genetics_study_id',
            'et_ophindnasample_sample_genetics_studies',
            'genetics_study_id',
            'genetics_study',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_genetics_study_id', 'et_ophindnasample_sample_genetics_studies');
        $this->dropForeignKey('fk_et_ophindnasample_sample_id', 'et_ophindnasample_sample_genetics_studies');
        $this->dropTable('et_ophindnasample_sample_genetics_studies');

        $this->dropColumn('et_ophindnasample_sample', 'other_sample_type');
        $this->dropColumn('et_ophindnasample_sample', 'consented_by');
        $this->dropColumn('et_ophindnasample_sample', 'is_local');
        $this->dropColumn('et_ophindnasample_sample', 'destination');
    }
}
