<?php

class m210904_102124_patient_consent_relationships extends OEMigration
{
    private $data = [
        ["display_order" => 1, "active" => 1, "name" => "Family"],
        ["display_order" => 2, "active" => 1, "name" => "Friend"],
        ["display_order" => 3, "active" => 1, "name" => "Carer"],
        ["display_order" => 4, "active" => 1, "name" => "Care home manager"],
        ["display_order" => 5, "active" => 1, "name" => "Social Worker"],
        ["display_order" => 6, "active" => 1, "name" => "Health professional"],
        ["display_order" => 7, "active" => 1, "name" => "GP"],
        ["display_order" => 8, "active" => 1, "name" => "Other"],
    ];

    private $data2 = [
        ["display_order" => 1, "active" => 1, "name" => "Face to face", "need_signature" => 1],
        ["display_order" => 2, "active" => 1, "name" => "Telephone", "need_signature" => 0],
        ["display_order" => 3, "active" => 1, "name" => "Other", "need_signature" => 2],
    ];

    public function safeUp()
    {
        $this->createOETable('ophtrconsent_patient_relationship',
            array(
                'id' => 'pk',
                'name' => 'varchar(255)',
                'active' => 'tinyint(1) default 1',
                'display_order' => 'int(10) unsigned not null default 1',
            ),
            false
        );

        $this->createOETable('ophtrconsent_patient_contact_method',
            array(
                'id' => 'pk',
                'name' => 'varchar(255)',
                //Required / Not Required / Optional
                'need_signature' => 'tinyint(1) not null',
                'active' => 'tinyint(1) default 1',
                'display_order' => 'int(10) unsigned not null default 1',
            ),
            false
        );

        $this->insertMultiple('ophtrconsent_patient_relationship', $this->data);
        $this->insertMultiple('ophtrconsent_patient_contact_method', $this->data2);
    }

    public function safeDown()
    {
        $this->dropOETable('ophtrconsent_patient_relationship', false);
        $this->dropOETable('ophtrconsent_patient_contact_method', false);
    }
}
