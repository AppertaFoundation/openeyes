<?php
class m210713_155701_create_tables_for_extraprocedure_element extends OEMigration
{
    public function safeUp()
    {
        $this->createElementGroupForEventType(
            'Extra Procedures',
            'OphTrConsent',
            40
        );
        $this->createElementType(
            'OphTrConsent',
            'Extra Procedures',
            array(
                'class_name'    => 'Element_OphTrConsent_ExtraProcedures',
                'display_order' => 50,
                'group_name'    => 'Extra Procedures',
                'default'       => 1
            )
        );
        $this->createOETable(
            'et_ophtrconsent_extraprocedure',
            array(
                'id'            => 'pk',
                'event_id'      => 'int(10) unsigned not null',
                'eye_id'        => 'int(10) unsigned',
                'anaesthetic_type_id' => 'int(10) unsigned',
                'booking_event_id'    => 'int(10) unsigned',
                'CONSTRAINT et_ophtrconsent_extraprocedure_ev_fk FOREIGN KEY (event_id) REFERENCES event (id)',
                'CONSTRAINT et_ophtrconsent_extraprocedure_eye_id_fk FOREIGN KEY (eye_id) REFERENCES eye (id)',
                'CONSTRAINT et_ophtrconsent_extraprocedure_booking_event_id_fk FOREIGN KEY (booking_event_id) REFERENCES event (id)',
                'CONSTRAINT et_ophtrconsent_extraprocedure_anaesthetic_type_id_fk FOREIGN KEY (anaesthetic_type_id) REFERENCES anaesthetic_type (id)',
            ),
            true
        );
    }
    public function safeDown()
    {
        $this->deleteElementType(
            'OphTrConsent',
            'Element_OphTrConsent_ExtraProcedures',
        );
        $this->deleteElementGroupForEventType(
            'Extra Procedures',
            'OphTrConsent'
        );
        $this->dropForeignKey(
            'et_ophtrconsent_extraprocedure_ev_fk',
            'Element_OphTrConsent_ExtraProcedures');
        $this->dropForeignKey(
            'et_ophtrconsent_extraprocedure_eye_id_fk',
            'Element_OphTrConsent_ExtraProcedures');
        $this->dropForeignKey(
            'et_ophtrconsent_extraprocedure_booking_event_id_fk',
            'Element_OphTrConsent_ExtraProcedures');
        $this->dropForeignKey(
                'et_ophtrconsent_extraprocedure_anaesthetic_type_id_fk',
                'Element_OphTrConsent_ExtraProcedures');
        $this->dropOETable(
            'et_ophtrconsent_extraprocedure',
            true
        );
    }
}
