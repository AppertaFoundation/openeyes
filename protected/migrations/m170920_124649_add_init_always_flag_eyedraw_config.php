<?php

class m170920_124649_add_init_always_flag_eyedraw_config extends OEMigration
{
    public function up()
    {
        $this->addColumn(
            'eyedraw_canvas_doodle',
            'eyedraw_always_init_canvas_flag',
            'tinyint(1) NOT NULL DEFAULT false'
        );
    }

    public function down()
    {
        $this->dropColumn('eyedraw_canvas_doodle', 'eyedraw_always_init_canvas_flag');
    }
}
