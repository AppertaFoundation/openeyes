<?php

class m191209_124917_add_comments_to_drops extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophciexamination_dilation', 'left_comments', 'text');
        $this->addColumn('et_ophciexamination_dilation_version', 'left_comments', 'text');
        $this->addColumn('et_ophciexamination_dilation', 'right_comments', 'text');
        $this->addColumn('et_ophciexamination_dilation_version', 'right_comments', 'text');
    }

    public function down()
    {
        $this->dropColumn('et_ophciexamination_dilation', 'left_comments');
        $this->dropColumn('et_ophciexamination_dilation', 'right_comments');
        $this->dropColumn('et_ophciexamination_dilation_version', 'left_comments');
        $this->dropColumn('et_ophciexamination_dilation_version', 'right_comments');
    }
}
