<?php

class m170418_120533_rename_elements extends CDbMigration
{
    public function up()
    {
        $this->execute("UPDATE element_type SET `name` = 'Adnexal' WHERE `name` = 'Adnexal Comorbidity'");
        $this->execute("UPDATE element_type SET `name` = 'Lids Medical' WHERE `name` = 'Medical Lids'");
    }

    public function down()
    {
        $this->execute("UPDATE element_type SET `name` = 'Adnexal Comorbidity' WHERE `name` = 'Adnexal'");
        $this->execute("UPDATE element_type SET `name` = 'Medical Lids' WHERE `name` = 'Lids Medical'");
    }

}