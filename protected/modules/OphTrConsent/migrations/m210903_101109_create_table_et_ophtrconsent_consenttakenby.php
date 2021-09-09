<?php

class m210903_101109_create_table_et_ophtrconsent_consenttakenby extends OEMigration
{
    public function up()
    {
        $this->createElementGroupForEventType(
            'Consent Taken by',
            'OphTrConsent',
            170
        );

        $element_type_id = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('class_name = :class_name', array(':class_name' => 'Element_OphTrConsent_Consenttakenby'))->queryScalar();

        if(!$element_type_id) {
            $this->createElementType(
                'OphTrConsent',
                'Consent Taken by',
                [
                    'class_name' => 'Element_OphTrConsent_Consenttakenby',
                    'display_order' => 250,
                    'group_name' => 'Consent Taken by',
                    'default' => 1,
                    'required' => 1,
                ]
            );
            $this->createOETable(
                'et_ophtrconsent_consenttakenby',
                [
                    'id' => 'pk',
                    'event_id' => 'int(10) unsigned not null',
                    'name_hp' => 'varchar(255)',
                    'second_op' => 'boolean not null default FALSE',
                    'sec_op_hp' => 'varchar (255)',
                    'consultant_id' => 'int(10) unsigned',
                ],
                true
            );
            $this->addForeignKey('et_ophtrconsent_consenttakenby_ev_fk', 'et_ophtrconsent_consenttakenby', 'event_id', 'event', 'id');
            $this->addForeignKey('constraint et_ophtrconsent_consenttakenby_consultant_fk', 'et_ophtrconsent_consenttakenby', 'consultant_id', 'user', 'id');
        }
    }

    public function down()
    {
        $this->deleteElementType(
            'OphTrConsent',
            'Element_OphTrConsent_Consenttakenby',
        );
        $this->deleteElementGroupForEventType(
            'Consent Taken by',
            'OphTrConsent'
        );
        $this->dropForeignKey('et_ophtrconsent_consenttakenby_ev_fk', 'et_ophtrconsent_consenttakenby');
        $this->dropForeignKey('et_ophtrconsent_consenttakenby_consultant_fk', 'et_ophtrconsent_consenttakenby');
        $this->dropOETable(
            'et_ophtrconsent_consenttakenby',
            true
        );
    }
}
