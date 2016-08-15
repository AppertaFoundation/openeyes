<?php

class m140502_141417_multiple_iop_readings extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'ophciexamination_intraocularpressure_value',
            array(
                'id' => 'pk',
                'element_id' => 'integer unsigned not null',
                'eye_id' => 'integer unsigned not null',
                'reading_time' => 'time not null',
                'reading_id' => 'integer unsigned not null',
                'instrument_id' => 'integer unsigned',
                'constraint ophciexamination_intraocularpressure_value_elid_fk foreign key(element_id) references et_ophciexamination_intraocularpressure(id)',
                'constraint ophciexamination_intraocularpressure_value_eid_fk foreign key(eye_id) references eye(id)',
                'constraint ophciexamination_intraocularpressure_value_rid_fk foreign key(reading_id) references ophciexamination_intraocularpressure_reading (id)',
                'constraint ophciexamination_intraocularpressure_value_iid_fk foreign key(instrument_id) references ophciexamination_instrument (id)',
            ),
            true
        );

        $this->execute(
            'insert into ophciexamination_intraocularpressure_value (element_id, eye_id, reading_time, instrument_id, reading_id) '.
            'select id, 1, time(created_date), left_instrument_id, left_reading_id from et_ophciexamination_intraocularpressure where left_reading_id != 1'
        );

        $this->execute(
            'insert into ophciexamination_intraocularpressure_value (element_id, eye_id, reading_time, instrument_id, reading_id) '.
            'select id, 2, time(created_date), right_instrument_id, right_reading_id from et_ophciexamination_intraocularpressure where right_reading_id != 1'
        );

        $this->addColumn('et_ophciexamination_intraocularpressure', 'left_comments', 'text');
        $this->addColumn('et_ophciexamination_intraocularpressure_version', 'left_comments', 'text');
        $this->addColumn('et_ophciexamination_intraocularpressure', 'right_comments', 'text');
        $this->addColumn('et_ophciexamination_intraocularpressure_version', 'right_comments', 'text');

        $this->update('et_ophciexamination_intraocularpressure', array('left_comments' => 'No reading (legacy)'), 'left_reading_id = 1');
        $this->update('et_ophciexamination_intraocularpressure', array('right_comments' => 'No reading (legacy)'), 'right_reading_id = 1');

        $this->dropForeignKey('et_ophciexamination_intraocularpressure_li_fk', 'et_ophciexamination_intraocularpressure');
        $this->dropColumn('et_ophciexamination_intraocularpressure', 'left_instrument_id');
        $this->dropColumn('et_ophciexamination_intraocularpressure_version', 'left_instrument_id');
        $this->dropForeignKey('et_ophciexamination_intraocularpressure_lri_fk', 'et_ophciexamination_intraocularpressure');
        $this->dropColumn('et_ophciexamination_intraocularpressure', 'left_reading_id');
        $this->dropColumn('et_ophciexamination_intraocularpressure_version', 'left_reading_id');
        $this->dropForeignKey('et_ophciexamination_intraocularpressure_ri_fk', 'et_ophciexamination_intraocularpressure');
        $this->dropColumn('et_ophciexamination_intraocularpressure', 'right_instrument_id');
        $this->dropColumn('et_ophciexamination_intraocularpressure_version', 'right_instrument_id');
        $this->dropForeignKey('et_ophciexamination_intraocularpressure_rri_fk', 'et_ophciexamination_intraocularpressure');
        $this->dropColumn('et_ophciexamination_intraocularpressure', 'right_reading_id');
        $this->dropColumn('et_ophciexamination_intraocularpressure_version', 'right_reading_id');

        $this->delete('ophciexamination_intraocularpressure_reading', 'id = 1');
    }

    public function down()
    {
        $this->insert('ophciexamination_intraocularpressure_reading', array('id' => 1, 'name' => 'NR', 'value' => null, 'display_order' => 1));

        $this->addColumn('et_ophciexamination_intraocularpressure', 'left_instrument_id', 'integer unsigned');
        $this->addColumn('et_ophciexamination_intraocularpressure_version', 'left_instrument_id', 'integer unsigned');
        $this->addColumn('et_ophciexamination_intraocularpressure', 'left_reading_id', 'integer unsigned not null');
        $this->addColumn('et_ophciexamination_intraocularpressure_version', 'left_reading_id', 'integer unsigned not null');
        $this->addColumn('et_ophciexamination_intraocularpressure', 'right_instrument_id', 'integer unsigned');
        $this->addColumn('et_ophciexamination_intraocularpressure_version', 'right_instrument_id', 'integer unsigned');
        $this->addColumn('et_ophciexamination_intraocularpressure', 'right_reading_id', 'integer unsigned not null');
        $this->addColumn('et_ophciexamination_intraocularpressure_version', 'right_reading_id', 'integer unsigned not null');

        $this->execute(
            'update et_ophciexamination_intraocularpressure e left join ophciexamination_intraocularpressure_value v on v.element_id = e.id and v.eye_id = 1 '.
            'set e.left_instrument_id = v.instrument_id, e.left_reading_id = IFNULL(v.reading_id, 1)'
        );

        $this->execute(
            'update et_ophciexamination_intraocularpressure e right join ophciexamination_intraocularpressure_value v on v.element_id = e.id and v.eye_id = 1 '.
            'set e.right_instrument_id = v.instrument_id, e.right_reading_id = IFNULL(v.reading_id, 1)'
        );

        $this->addForeignKey('et_ophciexamination_intraocularpressure_li_fk', 'et_ophciexamination_intraocularpressure', 'left_instrument_id', 'ophciexamination_instrument', 'id');
        $this->addForeignKey('et_ophciexamination_intraocularpressure_lri_fk', 'et_ophciexamination_intraocularpressure', 'left_reading_id', 'ophciexamination_intraocularpressure_reading', 'id');
        $this->addForeignKey('et_ophciexamination_intraocularpressure_ri_fk', 'et_ophciexamination_intraocularpressure', 'right_instrument_id', 'ophciexamination_instrument', 'id');
        $this->addForeignKey('et_ophciexamination_intraocularpressure_rri_fk', 'et_ophciexamination_intraocularpressure', 'right_reading_id', 'ophciexamination_intraocularpressure_reading', 'id');

        $this->dropColumn('et_ophciexamination_intraocularpressure', 'left_comments');
        $this->dropColumn('et_ophciexamination_intraocularpressure_version', 'left_comments');
        $this->dropColumn('et_ophciexamination_intraocularpressure', 'right_comments');
        $this->dropColumn('et_ophciexamination_intraocularpressure_version', 'right_comments');

        $this->dropTable('ophciexamination_intraocularpressure_value');
        $this->dropTable('ophciexamination_intraocularpressure_value_version');
    }
}
