<?php

class m211008_091100_remove_old_signature_element_type_fix extends OEMigration
{
    private const EVENT_TYPE = 'OphCoCorrespondence';
    private $classNames =
        '\'OEModule\\\OphCoCorrespondence\\\models\\\Element_Signature\',
         \'OEModule\\\OphCoCorrespondence\\\models\\\Element_Secondary_Signature\',
         \'OEModule\\\OphCoCorrespondence\\\models\\\Element_SecretarySignature\'';

    public function safeUp()
    {
        // OEMigration::deleteElementType() fails if the element type does not exist,
        // better do it ourselves
        $event_type_id = $this->getIdOfEventTypeByClassName(self::EVENT_TYPE);
        $this->execute("UPDATE element_type SET parent_element_type_id = NULL WHERE class_name IN ({$this->classNames})");
        $this->delete(
            'element_type',
            "event_type_id = $event_type_id AND class_name IN ({$this->classNames})"
        );
    }

    public function down()
    {
        // We don't know if this instance was migrated from 1.19
        echo __CLASS__." can't be reverted.".PHP_EOL;
        return false;
    }
}
