<?php

class m211126_145200_add_contact_fields_to_withdraw extends OEMigration
{
    private const table_name = "et_ophtrconsent_withdrawal";
    private const signatory_table = "et_ophtrconsent_withdrawal_patient_signature";
    private const default_contact_type = 1; //PATIENT_CONTACTS_TYPE
    private const unable_to_consent = [2,4];


    public function safeUp()
    {
        $this->addOEColumn(self::table_name, 'contact_type_id', 'tinyint(4) NULL', true);
        $this->addOEColumn(self::table_name, 'contact_user_id', 'int(10) unsigned default NULL', true);
        $this->addOEColumn(self::table_name, 'first_name', 'varchar(200) default NULL', true);
        $this->addOEColumn(self::table_name, 'last_name', 'varchar(200) default NULL', true);
        $this->addOEColumn(self::table_name, 'email', 'varchar(255) default NULL', true);
        $this->addOEColumn(self::table_name, 'phone_number', 'varchar(50) default NULL', true);
        $this->addOEColumn(self::table_name, 'mobile_number', 'varchar(50) default NULL', true);
        $this->addOEColumn(self::table_name, 'address_line1', 'varchar(255) default NULL', true);
        $this->addOEColumn(self::table_name, 'address_line2', 'varchar(255) default NULL', true);
        $this->addOEColumn(self::table_name, 'city', 'varchar(100) default NULL', true);
        $this->addOEColumn(self::table_name, 'country_id', 'int(10) unsigned default NULL', true);
        $this->addOEColumn(self::table_name, 'postcode', 'varchar(20) default NULL', true);
        $this->addOEColumn(self::table_name, 'consent_patient_relationship_id', 'int(11) NULL', true);
        $this->addOEColumn(self::table_name, 'other_relationship', 'varchar(200) default NULL', true);
        $this->addOEColumn(self::table_name, 'comment', 'text default NULL', true);

        // Migrate Signatory data from MEH
        if ($this->dbConnection->schema->getTable(self::signatory_table)) {
            $condition = implode(' OR ', self::unable_to_consent);
            $this->execute("
                UPDATE et_ophtrconsent_withdrawal w
                    LEFT JOIN `event` e ON e.id = w.event_id
                    LEFT JOIN episode ep ON ep.id = e.episode_id 
                    LEFT JOIN patient p ON p.id = ep.patient_id
                    LEFT JOIN contact c ON c.id = p.contact_id
                    LEFT JOIN address a ON a.contact_id = p.contact_id
                    LEFT JOIN et_ophtrconsent_withdrawal_patient_signature wps ON wps.event_id = e.id
                    LEFT JOIN et_ophtrconsent_type ct ON ct.event_id = e.id
                SET 
                    w.contact_type_id = " . self::default_contact_type . ",
                    w.contact_user_id = NULL,
                    w.first_name = CASE ct.type_id WHEN ".$condition." THEN wps.signatory_name ELSE c.first_name END,
                    w.last_name =  CASE ct.type_id WHEN ".$condition." THEN NULL ELSE c.last_name END,
                    w.email = CASE ct.type_id WHEN ".$condition." THEN NULL ELSE c.email END,
                    w.phone_number = CASE ct.type_id WHEN ".$condition." THEN NULL ELSE c.primary_phone END,
                    w.mobile_number = CASE ct.type_id WHEN ".$condition." THEN NULL ELSE c.mobile_phone END,
                    w.address_line1 = CASE ct.type_id WHEN ".$condition." THEN NULL ELSE a.address1 END,
                    w.address_line2 = CASE ct.type_id WHEN ".$condition." THEN NULL ELSE a.address2 END,
                    w.city = CASE ct.type_id WHEN ".$condition." THEN NULL ELSE a.city END,
                    w.country_id = CASE ct.type_id WHEN ".$condition." THEN NULL ELSE a.country_id END,
                    w.postcode = CASE ct.type_id WHEN ".$condition." THEN NULL ELSE a.postcode END,
                    w.consent_patient_relationship_id = ( SELECT id FROM ophtrconsent_patient_relationship WHERE LOWER(`name`) = CASE ct.type_id WHEN ".$condition." THEN 'family' ELSE 'other' END ),
                    w.other_relationship = CASE ct.type_id WHEN ".$condition." THEN 'Parent' ELSE 'Patient' END,
                    w.comment = NULL
                ;
            ");
        }

        $this->addForeignKey('fk_ophtrconsent_withdraw_contact_user', self::table_name, 'contact_user_id', 'user', 'id');
        $this->addForeignKey('fk_ophtrconsent_withdraw_contact_country', self::table_name, 'country_id', 'country', 'id');
        $this->addForeignKey('fk_ophtrconsent_withdraw_contact_rship', self::table_name, 'consent_patient_relationship_id', 'ophtrconsent_patient_relationship', 'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_ophtrconsent_withdraw_contact_user', self::table_name);
        $this->dropForeignKey('fk_ophtrconsent_withdraw_contact_country', self::table_name);
        $this->dropForeignKey('fk_ophtrconsent_withdraw_contact_rship', self::table_name);

        $this->dropOEColumn(self::table_name, 'contact_type_id', true);
        $this->dropOEColumn(self::table_name, 'contact_user_id', true);
        $this->dropOEColumn(self::table_name, 'first_name', true);
        $this->dropOEColumn(self::table_name, 'last_name', true);
        $this->dropOEColumn(self::table_name, 'email', true);
        $this->dropOEColumn(self::table_name, 'phone_number', true);
        $this->dropOEColumn(self::table_name, 'mobile_number', true);
        $this->dropOEColumn(self::table_name, 'address_line1', true);
        $this->dropOEColumn(self::table_name, 'address_line2', true);
        $this->dropOEColumn(self::table_name, 'city', true);
        $this->dropOEColumn(self::table_name, 'country_id', true);
        $this->dropOEColumn(self::table_name, 'postcode', true);
        $this->dropOEColumn(self::table_name, 'consent_patient_relationship_id', true);
        $this->dropOEColumn(self::table_name, 'other_relationship', true);
        $this->dropOEColumn(self::table_name, 'comment', true);
    }
}
