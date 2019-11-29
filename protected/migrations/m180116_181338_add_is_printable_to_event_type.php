<?php

class m180116_181338_add_is_printable_to_event_type extends CDbMigration
{
    public function up()
    {
        $this->addColumn('event_type', 'is_printable', 'tinyint(1) not null default 1');
        $this->addColumn('event_type_version', 'is_printable', 'tinyint(1) not null default 1');
        $this->execute("UPDATE event_type SET is_printable=0 where class_name in (
                            'OphTrLaser',
                            'OphOuAnaestheticsatisfactionaudit',
                            'OphTrOperationbooking', 
                            'OphCoTherapyapplication', 
                            'OphInBiometry',
                            'OphInVisualfields',
                            'OphCoDocument',
                            'OphInLabResults',
                            'Genetics',
                            'OphInGeneticresults',
                            'OphInDnaextraction',
                            'OphInDnasample',
                            'OphCoCvi')");
    }

    public function down()
    {
        $this->dropColumn('event_type', 'is_printable');
        $this->dropColumn('event_type_version', 'is_printable');
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