<?php

class m210803_211409_adding_footer_column_to_document_instance extends OEMigration
{
    public function up()
    {
        $this->addColumn('document_instance', 'footer', 'text default null');
    }

    public function down()
    {
        $this->dropColumn('document_instance', 'footer');
    }
}
