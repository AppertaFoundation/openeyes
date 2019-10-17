<?php

class m180601_104426_create_oescape_summary_table extends OEMigration
{

    public function safeUp()
    {
        $this->createOETable(
            'oescape_summary_item',
            array(
                'id' => 'int unsigned not null auto_increment primary key',
                'event_type_id' => 'int unsigned not null',
                'name' => 'varchar(85) not null',
                'unique (name, event_type_id)',
                'constraint oescape_summary_item_etid_fk foreign key (event_type_id) references event_type (id)',
            )
        );

        $this->createOETable(
            'oescape_summary',
            array(
                'id' => 'int unsigned not null auto_increment primary key',
                'item_id' => 'int unsigned not null',
                'subspecialty_id' => 'int unsigned default null',
                'display_order' => 'int unsigned not null',
                'unique (item_id, subspecialty_id)',
                'constraint oescape_summary_iid_fk foreign key (item_id) references oescape_summary_item (id)',
                'constraint oescape_summarry_ssid_fk foreign key (subspecialty_id) references subspecialty (id)',
            )
        );
    }

    public function safeDown()
    {
        $this->dropTable('oescape_summary');
        $this->dropTable('oescape_summary_item');
    }

}