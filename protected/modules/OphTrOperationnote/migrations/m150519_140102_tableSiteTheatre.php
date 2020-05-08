<?php

class m150519_140102_tableSiteTheatre extends OEMigration
{
    public function up()
    {
        $this->createOETable('et_ophtroperationnote_site_theatre', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'site_id' => 'int(10) unsigned NOT NULL',
            'theatre_id' => 'int(10) unsigned NULL default NULL',
        ), true);

        $this->addForeignKey(
            'et_ophtroperationnote_site_theatre_ev_fk',
            'et_ophtroperationnote_site_theatre',
            'event_id',
            'event',
            'id'
        );

        $this->addForeignKey(
            'et_ophtroperationnote_site_theatre_site_fk',
            'et_ophtroperationnote_site_theatre',
            'site_id',
            'site',
            'id'
        );

        if (in_array('ophtroperationbooking_operation_theatre', $this->dbConnection->getSchema()->getTableNames())) {
            $this->addForeignKey(
                'et_ophtroperationnote_site_theatre_theatre_fk',
                'et_ophtroperationnote_site_theatre',
                'theatre_id',
                'ophtroperationbooking_operation_theatre',
                'id'
            );
        }

        $eventTypeId = $this->dbConnection->createCommand()->select('id')->from('event_type')->where(
            'class_name = :class_name',
            array(':class_name' => 'OphTrOperationnote')
        )->queryScalar();

        $this->insert('element_type', array(
            'event_type_id' => $eventTypeId,
            'name' => 'Location',
            'class_name' => 'Element_OphTrOperationnote_SiteTheatre',
            'display_order' => 1,
            'default' => 1,
            'required' => 1,
        ));
    }

    public function down()
    {
        if (in_array('ophtroperationbooking_operation_theatre', $this->dbConnection->getSchema()->getTableNames())) {
            $this->dropForeignKey('et_ophtroperationnote_site_theatre_theatre_fk', 'et_ophtroperationnote_site_theatre');
        }
        $this->dropForeignKey('et_ophtroperationnote_site_theatre_site_fk', 'et_ophtroperationnote_site_theatre');
        $this->dropForeignKey('et_ophtroperationnote_site_theatre_ev_fk', 'et_ophtroperationnote_site_theatre');

        $this->dropOETable('et_ophtroperationnote_site_theatre', true);

        $this->delete('element_type', "class_name = 'Element_OphTrOperationnote_SiteTheatre'");
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
