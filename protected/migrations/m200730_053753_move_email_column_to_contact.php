<?php

class m200730_053753_move_email_column_to_contact extends OEMigration
{
    public function up()
    {
        $address_emails = Yii::app()->db->createCommand()
        ->select('email, contact_id')
        ->from('address')
        ->where('email IS NOT NULL')
        ->queryAll();

        $this->addOEColumn('contact', 'email', 'varchar(255) default null AFTER qualifications', true);

        foreach($address_emails as $address_email){
            $this->update(
                'contact',
                array(
                    'email' => $address_email['email'],
                ),
                'id = :contact_id',
                array(
                    ':contact_id' => $address_email['contact_id'],
                )
            );
        }

        $this->dropOEColumn('address', 'email', true);
    }

    public function down()
    {
        $contact_emails = Yii::app()->db->createCommand()
        ->select('email, id')
        ->from('contact')
        ->where('email IS NOT NULL')
        ->queryAll();

        $this->addOEColumn('address', 'email', 'varchar(255) default null AFTER country_id', true);


        foreach($contact_emails as $contact_email){
            $this->update(
                'address',
                array(
                    'email' => $contact_email['email'],
                ),
                'contact_id = :contact_id',
                array(
                    ':contact_id' => $contact_email['id'],
                )
            );
        }

        $this->dropOEColumn('contact', 'email', true);
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