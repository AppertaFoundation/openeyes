<?php

class m200720_012824_add_message_copyto extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn('et_ophcomessaging_message', 'cc_enabled', 'tinyint(1) unsigned DEFAULT 0', true);

        $this->createOETable('ophcomessaging_message_copyto_users', array(
            'id' => 'pk',
            'element_id' => 'int(10) unsigned NOT NULL',
            'user_id' => 'int(10) unsigned NOT NULL',
            'marked_as_read' => 'tinyint(1) unsigned DEFAULT 0',
            'KEY `ophcomessaging_message_copyto_users_element_id_fk` (`element_id`)',
            'KEY `ophcomessaging_message_copyto_users_user_id_fk` (`user_id`)',
            'CONSTRAINT `ophcomessaging_message_copyto_users_element_id_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophcomessaging_message` (`id`)',
            'CONSTRAINT `ophcomessaging_message_copyto_users_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)',
        ), true);
    }

    public function safeDown()
    {
        $this->dropOEColumn('et_ophcomessaging_message', 'cc_enabled', true);
        $this->dropOETable('ophcomessaging_message_copyto_users', true);
    }
}
