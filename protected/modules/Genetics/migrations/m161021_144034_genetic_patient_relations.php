<?php

class m161021_144034_genetic_patient_relations extends OEMigration
{
    protected $relationships = array(
        'Mother',
        'Father',
        'Sibling',
        'Cousin',
    );

    public function up()
    {
        $this->createOETable('genetics_relationship', array(
            'id' => 'pk',
            'relationship' => 'varchar(255)',
        ));

        $this->createOETable('genetics_patient_relationship', array(
            'id' => 'pk',
            'patient_id' => 'int(11)',
            'related_to_id' => 'int(11)',
            'relationship_id' => 'int(11)',
        ));

        foreach ($this->relationships as $relationship) {
            $this->insert('genetics_relationship', array('relationship' => $relationship));
        }
    }

    public function down()
    {
        $this->dropOETable('genetics_patient_relationship');
        $this->dropOETable('genetics_relationship');
    }
}
