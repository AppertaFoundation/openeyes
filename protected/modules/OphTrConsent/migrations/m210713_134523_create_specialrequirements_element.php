<?php

class m210713_134523_create_specialrequirements_element extends OEMigration
{

    public function safeUp()
    {
        $this->createElementGroupForEventType(
            'Special requirements',
            'OphTrConsent',
            250
        );

        $this->createElementType(
            'OphTrConsent',
            'Special requirements',
            array(
                'class_name'    => 'Element_OphTrConsent_Specialrequirements',
                'display_order' => 250,
                'group_name'    => 'Special requirements',
                'default'       => 1
            )
        );

        $this->createOETable(
            'et_ophtrconsent_specialreq',
            array(
                'id'            => 'pk',
                'event_id'      => 'int(10) unsigned not null',
                'specialreq'      => 'varchar(255)',
                'constraint et_ophtrconsent_specialreq_ev_fk foreign key (event_id) references event (id)'
            ),
            true
        );
    }


    public function safeDown()
    {
        $this->deleteElementType(
            'OphTrConsent',
            'Element_OphTrConsent_Specialrequirements',
        );

        $this->deleteElementGroupForEventType(
            'Special requirements',
            'OphTrConsent'
        );

        $this->dropOETable(
            'et_ophtrconsent_specialreq',
            true
        );
    }
}
