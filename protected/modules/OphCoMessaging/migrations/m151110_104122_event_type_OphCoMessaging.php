<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class m151110_104122_event_type_OphCoMessaging extends CDbMigration
{
    public function up()
    {
        if (!$this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphCoMessaging'))->queryRow()) {
            $group = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('name=:name', array(':name' => 'Communication events'))->queryRow();
            $this->insert('event_type', array('class_name' => 'OphCoMessaging', 'name' => 'Message', 'event_group_id' => $group['id']));
        }
        $event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphCoMessaging'))->queryRow();

        if (!$this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name and event_type_id=:eventTypeId', array(':name' => 'Message', ':eventTypeId' => $event_type['id']))->queryRow()) {
            $this->insert('element_type', array('name' => 'Message', 'class_name' => 'OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message', 'event_type_id' => $event_type['id'], 'display_order' => 1, 'required' => 1));
        }

        $element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id=:eventTypeId and name=:name', array(':eventTypeId' => $event_type['id'], ':name' => 'Message'))->queryRow();

        $this->createTable('ophcomessaging_message_message_type', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'name' => 'varchar(128) NOT NULL',
                'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'deleted' => 'tinyint(1) unsigned not null',
                'PRIMARY KEY (`id`)',
                'KEY `ophcomessaging_message_message_type_lmui_fk` (`last_modified_user_id`)',
                'KEY `ophcomessaging_message_message_type_cui_fk` (`created_user_id`)',
                'CONSTRAINT `ophcomessaging_message_message_type_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophcomessaging_message_message_type_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->createTable('ophcomessaging_message_message_type_version', array(
                'id' => 'int(10) unsigned NOT NULL',
                'name' => 'varchar(128) NOT NULL',
                'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'deleted' => 'tinyint(1) unsigned not null',
                'version_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
                'version_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'PRIMARY KEY (`version_id`)',
                'KEY `acv_ophcomessaging_message_message_type_lmui_fk` (`last_modified_user_id`)',
                'KEY `acv_ophcomessaging_message_message_type_cui_fk` (`created_user_id`)',
                'KEY `ophcomessaging_message_message_type_aid_fk` (`id`)',
                'CONSTRAINT `acv_ophcomessaging_message_message_type_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `acv_ophcomessaging_message_message_type_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophcomessaging_message_message_type_aid_fk` FOREIGN KEY (`id`) REFERENCES `ophcomessaging_message_message_type` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->insert('ophcomessaging_message_message_type', array('name' => 'General', 'display_order' => 1));

        $this->createTable('et_ophcomessaging_message', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'event_id' => 'int(10) unsigned NOT NULL',
                'for_the_attention_of_user_id' => 'int(10) unsigned NOT NULL',

                'message_type_id' => 'int(10) unsigned NOT NULL',

                'urgent' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',

                'message_text' => 'text DEFAULT \'\'',

                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'deleted' => 'tinyint(1) unsigned not null',
                'PRIMARY KEY (`id`)',
                'KEY `et_ophcomessaging_message_lmui_fk` (`last_modified_user_id`)',
                'KEY `et_ophcomessaging_message_cui_fk` (`created_user_id`)',
                'KEY `et_ophcomessaging_message_ev_fk` (`event_id`)',
                'KEY `et_ophcomessaging_message_ftao_fk` (`for_the_attention_of_user_id`)',
                'KEY `ophcomessaging_message_message_type_fk` (`message_type_id`)',
                'CONSTRAINT `et_ophcomessaging_message_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `et_ophcomessaging_message_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `et_ophcomessaging_message_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
                'CONSTRAINT `et_ophcomessaging_message_ftao_fk` FOREIGN KEY (`for_the_attention_of_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophcomessaging_message_message_type_fk` FOREIGN KEY (`message_type_id`) REFERENCES `ophcomessaging_message_message_type` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->createTable('et_ophcomessaging_message_version', array(
                'id' => 'int(10) unsigned NOT NULL',
                'event_id' => 'int(10) unsigned NOT NULL',
                'for_the_attention_of_user_id' => 'int(10) unsigned NOT NULL', // For the attention of
                'message_type_id' => 'int(10) unsigned NOT NULL', // Message Type
                'urgent' => 'tinyint(1) unsigned NOT NULL DEFAULT 0', // Priority
                'message_text' => 'text DEFAULT \'\'', // Message Text
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'deleted' => 'tinyint(1) unsigned not null',
                'version_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
                'version_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'PRIMARY KEY (`version_id`)',
                'KEY `acv_et_ophcomessaging_message_lmui_fk` (`last_modified_user_id`)',
                'KEY `acv_et_ophcomessaging_message_cui_fk` (`created_user_id`)',
                'KEY `acv_et_ophcomessaging_message_ev_fk` (`event_id`)',
                'KEY `et_ophcomessaging_message_aid_fk` (`id`)',
                'KEY `acv_et_ophcomessaging_message_ftao_fk` (`for_the_attention_of_user_id`)',
                'KEY `acv_ophcomessaging_message_message_type_fk` (`message_type_id`)',
                'CONSTRAINT `acv_et_ophcomessaging_message_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `acv_et_ophcomessaging_message_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `acv_et_ophcomessaging_message_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
                'CONSTRAINT `et_ophcomessaging_message_aid_fk` FOREIGN KEY (`id`) REFERENCES `et_ophcomessaging_message` (`id`)',
                'CONSTRAINT `acv_et_ophcomessaging_message_ftao_fk` FOREIGN KEY (`for_the_attention_of_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `acv_ophcomessaging_message_message_type_fk` FOREIGN KEY (`message_type_id`) REFERENCES `ophcomessaging_message_message_type` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
    }

    public function down()
    {
        $this->dropTable('et_ophcomessaging_message_version');
        $this->dropTable('et_ophcomessaging_message');

        $this->dropTable('ophcomessaging_message_message_type_version');
        $this->dropTable('ophcomessaging_message_message_type');

        $event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphCoMessaging'))->queryRow();

        foreach ($this->dbConnection->createCommand()->select('id')->from('event')->where('event_type_id=:event_type_id', array(':event_type_id' => $event_type['id']))->queryAll() as $row) {
            $this->delete('audit', 'event_id='.$row['id']);
            $this->delete('event', 'id='.$row['id']);
        }

        $this->delete('element_type', 'event_type_id='.$event_type['id']);
        $this->delete('event_type', 'id='.$event_type['id']);
    }
}
