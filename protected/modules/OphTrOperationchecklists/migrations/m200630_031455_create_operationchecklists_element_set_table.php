<?php

class m200630_031455_create_operationchecklists_element_set_table extends OEMigration
{
    private $element_sets = array (
        array (
            'id' => 1,
            'name' => 'Admission',
            'position' => 1,
        ),
        array (
            'id' => 2,
            'name' => 'Ward Practitioner',
            'position' => 2,
        ),
        array (
            'id' => 3,
            'name' => 'Reception Practitioner',
            'position' => 3,
        ),
        array (
            'id' => 4,
            'name' => 'Theatre Practitioner',
            'position' => 4,
        ),
        array (
            'id' => 5,
            'name' => 'Discharge',
            'position' => 5,
        ),
    );

    public function up()
    {
        $this->createOETable('ophtroperationchecklists_element_set', array(
            'id' => 'pk',
            'name' => 'varchar(40)',
            'position' => 'int(10) unsigned NOT NULL DEFAULT 1',
        ), true);

        foreach ($this->element_sets as $element_set) {
            $this->insert('ophtroperationchecklists_element_set', $element_set);
        }
    }

    public function down()
    {
        $this->dropOETable('ophtroperationchecklists_element_set', true);
    }
}
