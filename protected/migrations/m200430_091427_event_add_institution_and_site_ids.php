<?php

class m200430_091427_event_add_institution_and_site_ids extends OEMigration
{

    public function safeUp()
    {
        $institution_code = Yii::app()->params['institution_code'];

        $this->addOEColumn('event', 'institution_id', 'int(10) unsigned NOT NULL', true);
        $this->addOEColumn('event', 'site_id', 'int(10) unsigned', true);
        $institution = $this->dbConnection->createCommand('SELECT id FROM institution WHERE remote_id = :remote_id')
            ->bindValues(array(':remote_id' => $institution_code))
            ->queryScalar();

        $this->update('event', ['institution_id' => $institution]);

        $this->addForeignKey('fk_event_institution', 'event', 'institution_id', 'institution', 'id');
        $this->addForeignKey('fk_event_site', 'event', 'site_id', 'site', 'id');

        $this->execute("
            UPDATE event
            INNER JOIN et_ophcocorrespondence_letter letter ON event.id = letter.event_id
            INNER JOIN site ON site.id = letter.site_id
            SET event.site_id = letter.site_id,
            event.institution_id = site.institution_id
        ");
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_event_institution', 'event');
        $this->dropForeignKey('fk_event_site', 'event');
        $this->dropOEColumn('event', 'institution_id', true);
        $this->dropOEColumn('event', 'site_id', true);
    }
}
