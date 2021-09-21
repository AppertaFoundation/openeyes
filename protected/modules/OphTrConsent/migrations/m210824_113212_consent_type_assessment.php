<?php

class m210824_113212_consent_type_assessment extends OEMigration
{
    public function up()
    {

        $this->createOETable(
            'ophtrconsent_type_assessment',
            array(
                'id'            => 'pk',
                'element_id'    => 'int(10) unsigned',
                'type_id'       => 'int(10) unsigned',
                'display_order' => 'tinyint(4) NOT NULL DEFAULT 0'
            ),
            true
        );

        $this->addForeignKey(
            "fk_ophtrconsent_type_assessment_element",
            "ophtrconsent_type_assessment",
            "element_id",
            "element_type",
            "id"
        );

        $this->addForeignKey(
            "fk_ophtrconsent_type_assessment_type",
            "ophtrconsent_type_assessment",
            "type_id",
            "ophtrconsent_type_type",
            "id"
        );

        $this->execute("
            ALTER TABLE ophtrconsent_type_assessment
            ADD UNIQUE INDEX ophtrconsent_type_assessment_element (element_id, type_id);
        ");
    }

    public function down()
    {
        $this->dropForeignKey('fk_ophtrconsent_type_assessment_element', 'ophtrconsent_type_assessment');
        $this->dropForeignKey('fk_ophtrconsent_type_assessment_type', 'ophtrconsent_type_assessment');
        $this->dropOETable('ophtrconsent_type_assessment', true);
    }
}
