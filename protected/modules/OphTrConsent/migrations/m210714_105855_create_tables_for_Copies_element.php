<?php
/**
 * protected/modules/OphTrConsent/migrations/m210714_105855_create_tables_for_Copies_element.php
 *
 * @package default
 */

class m210714_105855_create_tables_for_Copies_element extends OEMigration
{
    public function safeUp()
    {
        $this->createElementGroupForEventType(
            'Copies',
            'OphTrConsent',
            210
        );
        $this->createElementType(
            'OphTrConsent',
            'Copies',
            array(
                'class_name'    => 'Element_OphTrConsent_Copies',
                'display_order' => 210,
                'group_name'    => 'Copies',
                'default'       => 1,
                'required'  => 1,
            )
        );
        //create second table for the options
        $this->createOETable(
            'ophtrconsent_paper_copies',
            array(
                'id'            => 'pk',
                'name'          => 'varchar(255)',
                'display_order' => 'int(10) unsigned',
            ),
            true
        );
        //insert the values for the options
        $this->insert(
            'ophtrconsent_paper_copies',
            array(
                'name' => 'Yes, (will print after save or sign )',
                'display_order' => '1',

            ),
        );
        $this->insert(
            'ophtrconsent_paper_copies',
            array(
                'name' => 'No',
                'display_order' => '2',
            ),
        );
        $this->insert(
            'ophtrconsent_paper_copies',
            array(
                'name' => 'N/A or not offered',
                'display_order' => '3',

            ),
        );
        $this->createOETable(
            'et_ophtrconsent_copies',
            array(
                'id'            => 'pk',
                'event_id'      => 'int(10) unsigned',
                'copies_id'     => 'int(11) not null',
                'constraint et_ophtrconsent_event_ev_fk foreign key (event_id) references event (id)',
                'constraint et_ophtrconsent_copies_fk foreign key (copies_id) references ophtrconsent_paper_copies (id)',
            ),
            true
        );
    }
    public function safeDown()
    {
        $this->deleteElementType(
            'OphTrConsent',
            'Element_OphTrConsent_Copies',
        );
        $this->deleteElementGroupForEventType(
            'Copies',
            'OphTrConsent'
        );
        $this->dropForeignKey(
            'et_ophtrconsent_copies_fk',
            'et_ophtrconsent_copies');
        $this->dropForeignKey(
            'constraint et_ophtrconsent_event_ev_fk',
            'et_ophtrconsent_copies');
        $this->dropOETable(
            'et_ophtrconsent_copies',
            true
        );
        $this->dropOETable(
            'ophtrconsent_paper_copies',
            true
        );
    }
}
