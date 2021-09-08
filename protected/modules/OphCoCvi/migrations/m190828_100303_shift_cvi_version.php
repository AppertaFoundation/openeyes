<?php

class m190828_100303_shift_cvi_version extends OEMigration
{
    public function up()
    {
      //  $this->shiftEventTypeVersion("CVI");
    }

    public function down()
    {
        $this->revertEventTypeVersion("CVI");
    }
}
