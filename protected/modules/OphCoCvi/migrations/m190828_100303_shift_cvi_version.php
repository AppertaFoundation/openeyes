<?php

class m190828_100303_shift_cvi_version extends OEMigration
{
    public function up()
    {

// I think we can delete this file

      //  $this->shiftEventTypeVersion("CVI");
    }

    public function down()
    {
        $this->revertEventTypeVersion("CVI");
    }
}
