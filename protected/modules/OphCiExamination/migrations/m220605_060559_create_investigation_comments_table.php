<?php

class m220605_060559_create_investigation_comments_table extends OEMigration
{
    public function up()
    {
        $this->createOETable('ophciexamination_investigation_comments', array(
            'id' => 'pk',
            'investigation_code' => 'int(11) NOT NULL',
            'comments' => 'text'
        ), true);

        $this->addForeignKey(
            'investigation_comments_fk',
            'ophciexamination_investigation_comments',
            'investigation_code',
            'et_ophciexamination_investigation_codes',
            'id'
        );
    }

    public function down()
    {
        $this->dropForeignKey('investigation_comments_fk', 'ophciexamination_investigation_comments');
        $this->dropOETable('ophciexamination_investigation_comments', true);
    }
}
