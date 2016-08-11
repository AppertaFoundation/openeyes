<?php

class m140609_171735_digital_instrument extends OEMigration
{
    public function up()
    {
        $this->createOETable('ophciexamination_qualitative_scale', array(
                'id' => 'pk',
                'name' => 'string NOT NULL',
        ), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->insert('ophciexamination_qualitative_scale', array('id' => 1, 'name' => 'Digital'));

        $this->createOETable('ophciexamination_qualitative_scale_value', array(
                'id' => 'pk',
                'scale_id' => 'int(11) not null',
                'name' => 'string NOT NULL',
                'display_order' => 'tinyint(1) unsigned not null',
                'KEY `ophciexamination_qualitative_scale_value_sca_fk` (`scale_id`)',
                'CONSTRAINT `ophciexamination_qualitative_scale_value_sca_fk` FOREIGN KEY (`scale_id`) REFERENCES `ophciexamination_qualitative_scale` (`id`)',
        ), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->insert('ophciexamination_qualitative_scale_value', array('id' => 1, 'name' => 'Soft', 'display_order' => 1, 'scale_id' => 1));
        $this->insert('ophciexamination_qualitative_scale_value', array('id' => 2, 'name' => 'Normal', 'display_order' => 2, 'scale_id' => 1));
        $this->insert('ophciexamination_qualitative_scale_value', array('id' => 3, 'name' => 'Hard', 'display_order' => 3, 'scale_id' => 1));

        $this->addColumn('ophciexamination_instrument', 'scale_id', 'int(11) null');
        $this->createIndex('ophciexamination_instrument_scale_id_fk', 'ophciexamination_instrument', 'scale_id');
        $this->addForeignKey('ophciexamination_instrument_scale_id_fk', 'ophciexamination_instrument', 'scale_id', 'ophciexamination_qualitative_scale', 'id');

        $this->addColumn('ophciexamination_instrument_version', 'scale_id', 'int(11) null');

        $this->insert('ophciexamination_instrument', array('id' => 8, 'name' => 'Digital', 'display_order' => 70, 'scale_id' => 1));

        $this->addColumn('ophciexamination_intraocularpressure_value', 'qualitative_reading_id', 'int(10) unsigned null');
        $this->alterColumn('ophciexamination_intraocularpressure_value', 'reading_id', 'int(10) unsigned null');

        $this->addColumn('ophciexamination_intraocularpressure_value_version', 'qualitative_reading_id', 'int(10) unsigned null');
        $this->alterColumn('ophciexamination_intraocularpressure_value_version', 'reading_id', 'int(10) unsigned null');
    }

    public function down()
    {
        $this->alterColumn('ophciexamination_intraocularpressure_value_version', 'reading_id', 'int(10) unsigned not null');
        $this->dropColumn('ophciexamination_intraocularpressure_value_version', 'qualitative_reading_id');

        $this->alterColumn('ophciexamination_intraocularpressure_value', 'reading_id', 'int(10) unsigned not null');
        $this->dropColumn('ophciexamination_intraocularpressure_value', 'qualitative_reading_id');

        $this->delete('ophciexamination_instrument', "name = 'Digital'");

        $this->dropColumn('ophciexamination_instrument_version', 'scale_id');

        $this->dropForeignKey('ophciexamination_instrument_scale_id_fk', 'ophciexamination_instrument');
        $this->dropColumn('ophciexamination_instrument', 'scale_id');

        $this->dropTable('ophciexamination_qualitative_scale_value_version');
        $this->dropTable('ophciexamination_qualitative_scale_value');
        $this->dropTable('ophciexamination_qualitative_scale_version');
        $this->dropTable('ophciexamination_qualitative_scale');
    }
}
