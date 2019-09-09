<?php

class m171212_162200_add_extra_vr_procedure extends OEMigration
{
    public function up()
    {

        # Check that these values do not already exist
        $is410564006 = $this->dbConnection->createCommand()->select('id')->from('proc')->where('snomed_code = :snomed', array(':snomed' => '410564006'))->queryRow();

        # Insert values if they don't already exist
        if ($is410564006['id'] == '') {
            $this->insert('proc', array(
                          'term' => 'Posterior subtenon steroid injection',
                          'short_format' => 'Subtenon Injection.',
                          'default_duration' => '15',
                          'snomed_code' => '410564006',
                          'snomed_term' => 'Posterior subtenon steroid injection',
                          'aliases' => 'subtenon injection steroid)',
                  ));
        }


    }

    public function down()
    {
        echo "Not supported here!\n";
        return true;
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
