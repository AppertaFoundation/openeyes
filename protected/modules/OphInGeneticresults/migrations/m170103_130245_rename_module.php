<?php

class m170103_130245_rename_module extends OEMigration
{
    public function up()
    {

        if ($this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInGenetictest'))->queryRow()) {
            $group = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('name=:name', array(':name' => 'Investigation events'))->queryRow();
            $rowID = $this->dbConnection->createCommand()->select('id')->from('event_type')->where(
                'class_name=:class_name',
                array(':class_name' => 'OphInGenetictest')
            )->queryRow();
            $this->update(
                'event_type',
                array('class_name' => 'OphInGeneticresults', 'name' => 'Genetic Results'),
                'id = ' . $rowID['id'] . ' AND event_group_id = ' . $group['id']
            );
        }

        $this->update("element_type", array("class_name" => "Element_OphInGeneticresults_Test"), "class_name = 'Element_OphInGenetictest_Test'");

        $this->renameTable('ophingenetictest_test_method', 'ophingeneticresults_test_method');
        $this->renameTable('ophingenetictest_test_effect', 'ophingeneticresults_test_effect');
        $this->renameTable('et_ophingenetictest_test', 'et_ophingeneticresults_test');

        $this->setEventTypeRBACSuffix('OphInGeneticresults', 'GeneticResults');

        $this->renameTable('ophingenetictest_external_source', 'ophingeneticresults_external_source');

        $this->versionExistingTable('ophingeneticresults_test_method');
        $this->versionExistingTable('ophingeneticresults_test_effect');

        $this->renameTable('et_ophingenetictest_test_version', 'et_ophingeneticresults_test_version');
    }

    public function down()
    {

        if ($this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInGeneticresults'))->queryRow()) {
            $group = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('name=:name', array(':name' => 'Investigation events'))->queryRow();
            $rowID = $this->dbConnection->createCommand()->select('id')->from('event_type')->where(
                'class_name=:class_name',
                array(':class_name' => 'OphInGeneticresults')
            )->queryRow();
            $this->update('event_type', array('class_name' => 'OphInGenetictest', 'name' => 'Genetic Results'), 'id = ' . $rowID['id'] . ' AND event_group_id = ' . $group['id']);
        }

        $this->update("element_type", array("class_name" => "Element_OphInGenetictest_Test"), "class_name = 'Element_OphInGeneticresults_Test'");

        $this->renameTable('ophingeneticresults_test_method', 'ophingenetictest_test_method');
        $this->renameTable('ophingeneticresults_test_effect', 'ophingenetictest_test_effect');
        $this->renameTable('et_ophingeneticresults_test', 'et_ophingenetictest_test');

        $this->setEventTypeRBACSuffix('OphInGenetictest', 'GeneticTest');

        $this->renameTable('ophingeneticresults_external_source', 'ophingenetictest_external_source');

        $this->versionExistingTable('ophingenetictest_test_method');
        $this->versionExistingTable('ophingenetictest_test_effect');

        $this->renameTable('et_ophingeneticresults_test_version', 'et_ophingenetictest_test_version');
    }

}
