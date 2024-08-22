<?php

class m220825_124636_add_event_subtype_to_message_type extends OEMigration
{
    public function safeUp()
    {
        $event_subtype_name = "Telephone Call";
        $event_subtype_icon = "i-CoTelephoneCall";

        // If the message type table doesn't have an event_subtype column then add it now.
        // It may have already been added by migration m220810_145245_archive_module in SupCoPhoneLog modile
        if (!$this->verifyColumnExists('ophcomessaging_message_message_type', 'event_subtype')) {
            $this->addOEColumn('ophcomessaging_message_message_type', 'event_subtype', 'varchar(100) NULL', true);
        }

        // use IGNORE INTO in case the item already exists
        $this->execute('INSERT IGNORE INTO event_subtype (event_subtype, dicom_modality_code, icon_name, display_name) VALUES ("' . $event_subtype_name . '" , "", "' . $event_subtype_icon . '", "' . $event_subtype_name . '")');

        // check for, and if not exist add Telephone Call message type
        $message_type_id = $this->getDbConnection()
                                ->createCommand('SELECT id FROM ophcomessaging_message_message_type WHERE LOWER(name) LIKE "%phone%" ORDER BY created_date DESC LIMIT 1')
                                ->queryScalar();

        if (!$message_type_id) {
            $this->getDbConnection()
                ->createCommand('INSERT INTO ophcomessaging_message_message_type (name, display_order) values ("Telephone Call", 3)')
                ->execute();
            $message_type_id = $this->getDbConnection()->getLastInsertID();
        };

        $this->update('ophcomessaging_message_message_type', ['event_subtype' => $event_subtype_name], 'id = ' . $message_type_id);
    }

    public function safeDown()
    {
        // No down provided - additional column is benign, and repetition of the up step will not fail due to column verification
        return true;
    }
}
