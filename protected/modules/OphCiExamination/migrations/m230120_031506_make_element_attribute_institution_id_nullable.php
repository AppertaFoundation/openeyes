<?php

class m230120_031506_make_element_attribute_institution_id_nullable extends OEMigration
{
    public function up()
    {
        $this->alterOEColumn(
            'ophciexamination_attribute',
            'institution_id',
            'int(10) unsigned',
            true
        );

        // Drop institution ID from current attributes.
        // This can be reassigned to specific institutions on a piecemeal basis from the admin screens.
        $this->update(
            'ophciexamination_attribute',
            array('institution_id' => null)
        );
    }

    public function down()
    {
        echo "This migration does not support down migration.\n";
        return false;
    }
}
