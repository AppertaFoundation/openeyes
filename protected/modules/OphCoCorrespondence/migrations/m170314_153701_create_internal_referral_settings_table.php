<?php

class m170314_153701_create_internal_referral_settings_table extends OEMigration
{
    public function safeUp()
    {
        Institution::$db = $this->dbConnection;
        $this->createOETable('ophcocorrespondence_internal_referral_settings', array(
            'id' => 'pk',
            'display_order' => "tinyint(3) unsigned DEFAULT '0'",
            'field_type_id' => 'int(10) unsigned NOT NULL',
            'key' =>  'varchar(64) NOT NULL',
            'name' => 'varchar(64) NOT NULL',
            'data' =>  'varchar(4096) NOT NULL',
            'default_value' =>  'varchar(64) NOT NULL',
        ), $versioned = true);

        $this->addForeignKey('ophcocorrespondence_int_ref_set_field_type_id_fk', 'ophcocorrespondence_internal_referral_settings', 'field_type_id', 'setting_field_type', 'id');
        $this->addForeignKey('ophcocorrespondence_int_ref_set_created_user_id_fk', 'ophcocorrespondence_internal_referral_settings', 'created_user_id', 'user', 'id');
        $this->addForeignKey('ophcocorrespondence_int_ref_set_last_modified_user_id_fk', 'ophcocorrespondence_internal_referral_settings', 'last_modified_user_id', 'user', 'id');

        $this->createOETable('setting_internal_referral', array(
            'id' => 'pk',
            'element_type_id' => 'int(10) unsigned DEFAULT NULL',
            'key' =>  'varchar(64) NOT NULL',
            'value' => 'varchar(255) COLLATE utf8_bin NOT NULL',
        ), $versioned = true);

        $this->insert('ophcocorrespondence_internal_referral_settings', array(
            'field_type_id' => 3,
            'key' => 'is_enabled',
            'name' => 'Enable Internal referral',
            'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'default_value' => 'on'
        ));

        $this->insert('setting_field_type', array(
            'name' => 'Textarea'
        ));

        $textarea = $this->dbConnection->createCommand('SELECT * FROM setting_field_type WHERE name = "Textarea"')
            ->queryScalar();

        $this->insert('ophcocorrespondence_internal_referral_settings', array(
            'field_type_id' => $textarea,
            'key' => 'internal_referral_booking_address',
            'name' => 'Booking Address'
        ));

        $this->alterColumn('document_target', 'contact_type', "enum('PATIENT','GP','DRSS','LEGACY','OTHER', 'INTERNALREFERRAL') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'OTHER'");
        $this->alterColumn('document_output', 'output_type', 'varchar(20) COLLATE utf8_unicode_ci NOT NULL');

        $internal_referral_booking_address = $this->dbConnection
            ->createCommand('SELECT * FROM setting_internal_referral WHERE `key` = "internal_referral_booking_address"')
            ->queryRow();

        $address = Institution::model()->getCurrent()->name . "\r" . (implode("\r", Institution::model()->getCurrent()->getLetterAddress()));
        if ($internal_referral_booking_address) {
            $this->update(
                'setting_internal_referral',
                array(
                    'value' => $address,
                ),
                'id = :id',
                array(':id' => $internal_referral_booking_address['id'])
            );
        } else {
            $this->insert(
                'setting_internal_referral',
                array(
                    '`key`' => 'internal_referral_booking_address',
                    'value' => $address,
                )
            );
        }

        $this->insert('ophcocorrespondence_internal_referral_settings', array(
            'field_type_id' => 4,
            'key' => 'internal_referral_method_label',
            'name' => 'Delivery Method Label'
        ));
        $delivery_method_label = $this->dbConnection
            ->createCommand('SELECT * FROM setting_internal_referral WHERE `key` = "internal_referral_method_label"')
            ->queryRow();

        if ($delivery_method_label) {
            $this->update(
                'setting_internal_referral',
                array('value' => 'Electronic (WinDip)'),
                'id = :id',
                array(':id' => $delivery_method_label->id)
            );
        } else {
            $this->insert('setting_internal_referral', array('`key`' => 'internal_referral_method_label', 'value' => 'Electronic (WinDip)'));
        }
    }

    public function down()
    {
        $this->dropOETable('ophcocorrespondence_internal_referral_settings', $versioned = true);
        $this->dropOETable('setting_internal_referral', $versioned = true);

        $this->alterColumn('document_target', 'contact_type', "enum('PATIENT','GP','DRSS','LEGACY','OTHER') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'OTHER'");
        $this->alterColumn('document_output', 'output_type', 'varchar(10) COLLATE utf8_unicode_ci NOT NULL');
    }
}
