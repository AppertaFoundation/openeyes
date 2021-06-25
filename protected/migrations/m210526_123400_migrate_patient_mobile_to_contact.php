<?php

class m210526_123400_migrate_patient_mobile_to_contact extends OEMigration
{
    protected $patient_table = null;
    protected $contact_table = null;

    public function __construct()
    {
        $this->patient_table = Yii::app()->db->schema->getTable('patient', true);
        $this->contact_table = Yii::app()->db->schema->getTable('contact', true);
    }

    public function safeUp()
    {
        if (isset($this->patient_table->columns['mobile_telephone'])) {
            $this->execute('
                UPDATE contact AS c
                LEFT JOIN patient AS p ON p.contact_id = c.id
                SET c.mobile_phone = p.mobile_telephone
                WHERE p.mobile_telephone IS NOT NULL
                AND p.contact_id IS NOT NULL;
            ');

            $this->dropColumn('patient', 'mobile_telephone');
            $this->dropColumn('patient_version', 'mobile_telephone');
        }
    }

    public function safeDown()
    {
        if (isset($this->contact_table->columns['mobile_phone'])) {
            $this->addOEColumn('patient', 'mobile_telephone', 'varchar(50)', true);

            $this->execute('
                UPDATE patient AS p
                LEFT JOIN contact AS c ON c.id = p.contact_id
                SET p.mobile_telephone = c.mobile_phone
                WHERE p.contact_id IS NOT NULL
                AND c.mobile_phone IS NOT NULL;
            ');
        }
    }
}
