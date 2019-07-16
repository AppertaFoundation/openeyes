<?php

class m180713_121916_add_comments_opnote_cataract extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophtroperationnote_cataract' , 'comments' , 'text');
        $this->addColumn('et_ophtroperationnote_cataract_version' , 'comments' , 'text');
    }

    public function down()
    {
       $this->dropColumn('et_ophtroperationnote_cataract' , 'comments');
       $this->dropColumn('et_ophtroperationnote_cataract_version' , 'comments');
    }
}