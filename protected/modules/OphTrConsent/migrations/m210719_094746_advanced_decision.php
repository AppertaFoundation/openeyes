<?php
class m210719_094746_advanced_decision extends OEMigration
{
    public function safeUp()
    {
        $this->createElementGroupForEventType(
                'Advanced Decision',
                'OphTrConsent',
                140
        );
        $this->createElementType(
            'OphTrConsent',
            'Advanced Decision',
            array(
                'class_name' => 'Element_OphTrConsent_AdvancedDecision',
                'display_order' => 140,
                'group_name' => 'Advanced Decision',
                'default' => 1,
                'required' => 1
            )
        );
        $this->createOETable(
            'et_ophtrconsent_advanceddecision',
            array(
                'id' => 'pk',
                'event_id' => 'int(10) unsigned not null',
                'description' => 'text default null',
                'constraint et_ophtrconsent_advanceddecision_ev_fk foreign key (event_id) references event (id)'
            ),
            true
        );
    }
    public function safeDown()
    {
        $this->deleteElementType(
            'OphTrConsent',
            'Element_OphTrConsent_AdvancedDecision',
        );
        $this->deleteElementGroupForEventType(
            'Advanced Decision',
            'OphTrConsent'
        );
        $this->dropForeignKey(
            'et_ophtrconsent_advanceddecision_ev_fk',
            'et_ophtrconsent_advanceddecision'
        );
        $this->dropOETable(
            'et_ophtrconsent_advanceddecision',
            true
        );
    }
}
