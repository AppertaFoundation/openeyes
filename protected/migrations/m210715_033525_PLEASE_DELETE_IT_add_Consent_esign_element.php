<?php

class m210715_033525_PLEASE_DELETE_IT_add_Consent_esign_element extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->createElementType('OphTrConsent', 'E-Sign', array(
            'class_name' => 'Element_OphTrConsent_Esign',
            'display_order' => 10,
            'parent_class' => null
        ));

        $this->createOETable(
            'et_ophtrconsent_esign',
            array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned',
            'consultant_id' => 'int(10) unsigned',
            'second_opinion_id' => 'int(10) unsigned',
            'CONSTRAINT `et_ophtrconsent_esign_consult_id_fk` FOREIGN KEY (`consultant_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `et_ophtrconsent_esign_secop_id_fk` FOREIGN KEY (`second_opinion_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `et_ophtrconsent_esign_e_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
            ),
            true
        );

    }

    public function safeDown()
    {
        $this->deleteElementType('OphTrConsent','Element_OphTrConsent_Esign');
        $this->dropOETable('et_ophtrconsent_esign', true);
    }
}
