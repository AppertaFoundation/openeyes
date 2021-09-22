<?php

class m161108_162417_consentable_study_participation extends CDbMigration
{
    public function up()
    {
        $this->addColumn('genetics_study_subject', 'is_consent_given', 'tinyint');
        $this->addColumn('genetics_study_subject', 'consent_received_by', 'int(10) unsigned');
        $this->addColumn('genetics_study_subject', 'consent_given_on', 'datetime');
        $this->addColumn('genetics_study_subject', 'comments', 'text');
    }

    public function down()
    {
        $this->dropColumn('genetics_study_subject', 'is_consent_given');
        $this->dropColumn('genetics_study_subject', 'consent_received_by');
        $this->dropColumn('genetics_study_subject', 'consent_given_on');
        $this->dropColumn('genetics_study_subject', 'comments');
    }
}
