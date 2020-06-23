<?php

class m170831_083044_add_risk_tags extends OEMigration
{
    public function up()
    {
        $this->createOETable('ophciexamination_risk_tag', array(
            'risk_id' => 'int(11)',
            'tag_id' => 'int(11)'
        ), false);
        $this->addForeignKey(
            'ophciexamination_risk_tag_risk_fk',
            'ophciexamination_risk_tag',
            'risk_id',
            'ophciexamination_risk',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'ophciexamination_risk_tag_tag_fk',
            'ophciexamination_risk_tag',
            'tag_id',
            'tag',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropOETable('ophciexamination_risk_tag', false);
    }
}
