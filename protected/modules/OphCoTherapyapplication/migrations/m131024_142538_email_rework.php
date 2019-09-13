<?php

class m131024_142538_email_rework extends CDbMigration
{
    public function up()
    {
        // New table to allow multiple emails per event - email is no longer an element
        $this->createTable(
            'ophcotherapya_email',
            array(
                'id' => 'int unsigned not null auto_increment primary key',
                'event_id' => 'int unsigned not null',
                'eye_id' => 'tinyint not null',
                'email_text' => 'text',
                'archived' => 'tinyint not null default 0',
                'last_modified_user_id' => 'int unsigned not null',
                'last_modified_date' => 'datetime not null',
                'created_user_id' => 'int unsigned not null',
                'created_date' => 'datetime not null',
                'key (created_date)',
                'foreign key ophcotherapya_email_ei_fk (event_id) references event (id)',
                'foreign key ophcotherapya_email_lmui_fk (last_modified_user_id) references user (id)',
                'foreign key ophcotherapya_email_cui_fk (created_user_id) references user (id)',
            ),
            'engine=innodb CHARSET=utf8 COLLATE=utf8_unicode_ci'
        );

        // Populate the above - note that we're throwing away unsent emails
        $sql = 'insert into ophcotherapya_email (event_id, eye_id, email_text, last_modified_user_id, last_modified_date, created_user_id, created_date) '.
            'select event_id, %s, last_modified_user_id, last_modified_date, created_user_id, created_date from et_ophcotherapya_email where eye_id in (%s) and sent = 1';

        $this->execute(sprintf($sql, '1, left_email_text', '1, 3'));
        $this->execute(sprintf($sql, '2, right_email_text', '2, 3'));

        // New version of ophcotherapya_email_attachment referencing ophcotherapya_email
        $this->createTable(
            'ophcotherapya_email_attachment_new',
            array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'email_id' => 'int unsigned not null',
                'file_id' => 'int(10) unsigned NOT NULL',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `et_ophcotherapya_email_att_lmui_fk` (`last_modified_user_id`)',
                'KEY `et_ophcotherapya_email_att_cui_fk` (`created_user_id`)',
                'KEY `et_ophcotherapya_email_att_fi_fk` (`file_id`)',
            ),
            'engine=innodb CHARSET=utf8 COLLATE=utf8_unicode_ci'
        );

        $this->execute(
            'insert into ophcotherapya_email_attachment_new (id, email_id, file_id, last_modified_user_id, last_modified_date, created_user_id, created_date) '.
            'select a.id, e.id, a.file_id, a.last_modified_user_id, a.last_modified_date, a.created_user_id, a.created_date '.
            'from ophcotherapya_email e '.
            'inner join et_ophcotherapya_email ee on ee.event_id = e.event_id '.
            'inner join ophcotherapya_email_attachment a on a.element_id = ee.id and a.eye_id = e.eye_id'
        );

        // Now the destructive stuff - put the new ophcotherapya_email_attachment in place and remove the email element
        $this->execute('rename table ophcotherapya_email_attachment to ophcotherapya_email_attachment_old, ophcotherapya_email_attachment_new to ophcotherapya_email_attachment');
        $this->dropTable('ophcotherapya_email_attachment_old');

        $this->addForeignKey('et_ophcotherapya_email_att_ei_fk', 'ophcotherapya_email_attachment', 'email_id', 'ophcotherapya_email', 'id');
        $this->addForeignKey('et_ophcotherapya_email_att_fi_fk', 'ophcotherapya_email_attachment', 'file_id', 'protected_file', 'id');
        $this->addForeignKey('et_ophcotherapya_email_att_lmui_fk', 'ophcotherapya_email_attachment', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('et_ophcotherapya_email_att_cui_fk', 'ophcotherapya_email_attachment', 'created_user_id', 'user', 'id');

        $this->delete('element_type', 'class_name = ?', array('Element_OphCoTherapyapplication_Email'));
        $this->dropTable('et_ophcotherapya_email');
    }

    public function down()
    {
        $this->createTable(
            'et_ophcotherapya_email',
            array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'event_id' => 'int(10) unsigned NOT NULL',
                'eye_id' => 'int(10) unsigned NOT NULL DEFAULT 3 ',
                'left_email_text' => 'text',
                'right_email_text' => 'text',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'sent' => 'tinyint unsigned not null default 0',
                'PRIMARY KEY (`id`)',
                'KEY `et_ophcotherapya_email_lmui_fk` (`last_modified_user_id`)',
                'KEY `et_ophcotherapya_email_cui_fk` (`created_user_id`)',
                'KEY `et_ophcotherapya_email_ev_fk` (`event_id`)',
                'CONSTRAINT `et_ophcotherapya_email_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `et_ophcotherapya_email_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `et_ophcotherapya_email_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
            ),
            'engine=innodb CHARSET=utf8 COLLATE=utf8_unicode_ci'
        );

        // NB this does not restore unsent emails because we've scrapped them (and obviously any archived emails will be lost)
        $this->execute(
            'insert into et_ophcotherapya_email (event_id, last_modified_user_id, last_modified_date, created_user_id, created_date, sent) '.
            'select event_id, last_modified_user_id, last_modified_date, created_user_id, created_date, 1 '.
            'from ophcotherapya_email e where e.created_date = (select max(created_date) from ophcotherapya_email where event_id = e.event_id) group by e.event_id'
        );
        $this->execute(
            'update et_ophcotherapya_email e '.
            'left join ophcotherapya_email le on le.event_id = e.event_id and le.eye_id = 1 '.
            '                                    and le.created_date = (select max(created_date) from ophcotherapya_email where event_id = le.event_id and eye_id = 1) '.
            'left join ophcotherapya_email re on re.event_id = e.event_id and re.eye_id = 2 '.
            '                                    and re.created_date = (select max(created_date) from ophcotherapya_email where event_id = re.event_id and eye_id = 2) '.
            'set e.eye_id = if (le.id is not null and re.id is not null, 3, if (le.id is not null, 1, 2)), '.
            '    e.left_email_text = le.email_text, '.
            '    e.right_email_text = re.email_text'
        );

        $this->createTable(
            'ophcotherapya_email_attachment_old',
            array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'element_id' => 'int(10) unsigned NOT NULL',
                'eye_id' => 'int(10) unsigned NOT NULL',
                'file_id' => 'int(10) unsigned NOT NULL',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `et_ophcotherapya_email_att_lmui_fk` (`last_modified_user_id`)',
                'KEY `et_ophcotherapya_email_att_cui_fk` (`created_user_id`)',
                'KEY `et_ophcotherapya_email_att_ei_fk` (`element_id`)',
                'KEY `et_ophcotherapya_email_att_eyei_fk` (`eye_id`)',
                'KEY `et_ophcotherapya_email_att_fi_fk` (`file_id`)',

            ),
            'engine=innodb CHARSET=utf8 COLLATE=utf8_unicode_ci'
        );

        $this->execute(
            'insert into ophcotherapya_email_attachment_old (id, element_id, eye_id, file_id, last_modified_user_id, last_modified_date, created_user_id, created_date) '.
            'select a.id, ee.id, e.eye_id, a.file_id, a.last_modified_user_id, a.last_modified_date, a.created_user_id, a.created_date '.
            'from et_ophcotherapya_email ee '.
            'inner join ophcotherapya_email e on e.event_id = ee.event_id '.
            'inner join ophcotherapya_email_attachment a on a.email_id = e.id'
        );

        $this->execute('rename table ophcotherapya_email_attachment to ophcotherapya_email_attachment_new, ophcotherapya_email_attachment_old to ophcotherapya_email_attachment');
        $this->dropTable('ophcotherapya_email_attachment_new');

        $this->dropTable('ophcotherapya_email');

        $this->addForeignKey('et_ophcotherapya_email_att_lmui_fk', 'ophcotherapya_email_attachment', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('et_ophcotherapya_email_att_cui_fk', 'ophcotherapya_email_attachment', 'created_user_id', 'user', 'id');
        $this->addForeignKey('et_ophcotherapya_email_att_ei_fk', 'ophcotherapya_email_attachment', 'element_id', 'et_ophcotherapya_email', 'id');
        $this->addForeignKey('et_ophcotherapya_email_att_eyei_fk', 'ophcotherapya_email_attachment', 'eye_id', 'eye', 'id');
        $this->addForeignKey('et_ophcotherapya_email_att_fi_fk', 'ophcotherapya_email_attachment', 'file_id', 'protected_file', 'id');
    }
}
