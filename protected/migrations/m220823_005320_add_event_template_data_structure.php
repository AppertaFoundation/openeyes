<?php

class m220823_005320_add_event_template_data_structure extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'event_template',
            array(
                'id' => 'pk',
                'name' => 'varchar(100) NOT NULL',
                'event_type_id' => 'int unsigned NOT NULL',
                'source_event_id' => 'int unsigned NOT NULL',
            ),
            true
        );

        $this->createOETable(
            'proc_set',
            array(
                'id' => 'pk',
            ),
            true
        );

        $this->createOETable(
            'proc_set_assignment',
            array(
                'id' => 'pk',
                'proc_set_id' => 'int NOT NULL',
                'proc_id' => 'int(10) unsigned NOT NULL'
            ),
            true
        );

        $this->createOETable(
            'ophtroperationnote_template',
            array(
                'id' => 'pk',
                'event_template_id' => 'int NOT NULL',
                'proc_set_id' => 'int NOT NULL',
                'template_data' => 'JSON NOT NULL',
            ),
            true
        );

        $this->createOETable(
            'event_template_user',
            array(
                'id' => 'pk',
                'event_template_id' => 'int NOT NULL',
                'user_id' => 'int(10) unsigned NOT NULL'
            ),
            true
        );

        $this->addForeignKey(
            'event_template_event_type_fk',
            'event_template',
            'event_type_id',
            'event_type',
            'id'
        );

        $this->addForeignKey(
            'event_template_e_fk',
            'event_template',
            'source_event_id',
            'event',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationnote_template_t_fk',
            'ophtroperationnote_template',
            'event_template_id',
            'event_template',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationnote_template_ps_fk',
            'ophtroperationnote_template',
            'proc_set_id',
            'proc_set',
            'id'
        );

        $this->addForeignKey(
            'proc_set_assignment_ps_fk',
            'proc_set_assignment',
            'proc_set_id',
            'proc_set',
            'id'
        );

        $this->addForeignKey(
            'proc_set_assignment_p_fk',
            'proc_set_assignment',
            'proc_id',
            'proc',
            'id'
        );

        $this->addForeignKey(
            'event_template_user_t_fk',
            'event_template_user',
            'event_template_id',
            'event_template',
            'id'
        );

        $this->addForeignKey(
            'event_template_user_u_fk',
            'event_template_user',
            'user_id',
            'user',
            'id'
        );

        $this->addOEColumn('event', 'template_id', 'int', true);
        $this->addOEColumn('event_type', 'template_class_name', 'varchar(255)', true);

        $this->update('event_type', ['template_class_name' => 'OphTrOperationnote_Template'], 'class_name = "OphTrOperationnote"');
    }

    public function down()
    {
        $this->dropOEColumn('event_type', 'template_class_name', true);
        $this->dropOEColumn('event', 'template_id', true);

        $this->dropOETable(
            'ophtroperationnote_template',
            true
        );

        $this->dropOETable(
            'event_template_user',
            true
        );

        $this->dropOETable(
            'event_template',
            true
        );

        $this->dropOETable(
            'proc_set_assignment',
            true
        );

        $this->dropOETable(
            'proc_set',
            true
        );
    }
}
