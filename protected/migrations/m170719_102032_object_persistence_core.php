<?php

class m170719_102032_object_persistence_core extends OEMigration
{
    public function up()
    {
        $this->createTable('eyedraw_doodle', array(
            'eyedraw_class_mnemonic' => 'varchar(80) NOT NULL',
            'init_doodle_json' => 'text',
            'processed_canvas_intersection_tuple' => 'varchar(2000)' // automatically generated internally
        ));

        $this->addPrimaryKey('eyedraw_doodle_pk', 'eyedraw_doodle', 'eyedraw_class_mnemonic');
        $this->createTable('eyedraw_canvas', array(
            'canvas_mnemonic' => 'varchar(30) NOT NULL',
            'canvas_name' => 'varchar(60) NOT NULL',
            'container_element_type_id' => 'int(10) unsigned NOT NULL'
        ));
        $this->addPrimaryKey('eyedraw_canvas_pk', 'eyedraw_canvas', 'canvas_mnemonic');
        $this->addForeignKey('eyedraw_canvas_elid_fk', 'eyedraw_canvas', 'container_element_type_id',
            'element_type', 'id');

        $this->createTable('eyedraw_canvas_doodle', array(
            'eyedraw_class_mnemonic' => 'varchar(80) NOT NULL',
            'canvas_mnemonic' => 'varchar(30) NOT NULL',
            'eyedraw_on_canvas_toolbar_location' => 'varchar(15) NULL',
            'eyedraw_on_canvas_toolbar_order' => 'int(3) NULL',
            'eyedraw_no_tuple_init_canvas_flag' => 'tinyint(1) NOT NULL',
            'eyedraw_carry_forward_canvas_flag' => 'tinyint(1) NOT NULL'
        ));

        $this->addPrimaryKey('eyedraw_canvas_doodle_pk', 'eyedraw_canvas_doodle', 'eyedraw_class_mnemonic, canvas_mnemonic');
        $this->createIndex('eyedraw_canvas_doodle_edclmn_idx', 'eyedraw_canvas_doodle', 'eyedraw_class_mnemonic');
        $this->createIndex('eyedraw_canvas_doodle_cvmn_idx', 'eyedraw_canvas_doodle', 'canvas_mnemonic');
        $this->addForeignKey('eyedraw_canvas_doodle_cvmn_fk', 'eyedraw_canvas_doodle', 'canvas_mnemonic',
            'eyedraw_canvas', 'canvas_mnemonic');
        $this->addForeignKey('eyedraw_canvas_doodle_edclmn_fk', 'eyedraw_canvas_doodle', 'eyedraw_class_mnemonic',
            'eyedraw_doodle', 'eyedraw_class_mnemonic');

        $this->createTable('mview_datapoint_node', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'parent_datapoint_node_id' => 'int(11) unsigned',
            'eyedraw_class_mnemonic' => 'varchar(80) NOT NULL',
            'canvas_mnemonic' => 'varchar(30) NOT NULL',
            'placement_order' => 'int(6) NOT NULL',
            'laterality' => 'varchar(1) NOT NULL',
            'content_json' => 'varchar(4000) NOT NULL'
        ));
        $this->addForeignKey('mview_datapoint_node_evid_fk', 'mview_datapoint_node', 'event_id',
            'event', 'id');
        $this->addForeignKey('mview_datapoint_node_ecmn_fk', 'mview_datapoint_node', 'eyedraw_class_mnemonic, canvas_mnemonic',
            'eyedraw_canvas_doodle', 'eyedraw_class_mnemonic, canvas_mnemonic');
    }

    public function down()
    {
        $this->dropTable('mview_datapoint_node');
        $this->dropTable('eyedraw_canvas_doodle');
        $this->dropTable('eyedraw_canvas');
        $this->dropTable('eyedraw_doodle');
    }
}