<?php

class m200517_044325_add_multiple_LDAP_auth_to_institutions extends OEMigration
{
    /**
     * @return bool|void
     * @throws CException
     * @throws Exception
     */
    public function safeUp()
    {
        $this->createTable('user_authentication_method',
            [
                'code' => 'VARCHAR(64) NOT NULL',
                'CONSTRAINT PK_user_auth_code PRIMARY KEY (code)'
            ],
            'engine=InnoDB charset=utf8 collate=utf8_unicode_ci'
        );
        $this->createOETable('ldap_config',
            [
                'id' => 'pk',
                'description' => 'VARCHAR(255) NOT NULL',
                'ldap_json' => 'TEXT NULL',
            ],
            true,
            'ldap_conf'
        );
        $this->createOETable('institution_authentication',
            [
                'id' => 'pk',
                'site_id' => 'INT(10) UNSIGNED NULL DEFAULT NULL',
                'institution_id' => 'INT(10) UNSIGNED NOT NULL',
                'user_authentication_method' => 'VARCHAR(64) NOT NULL',
                'ldap_config_id' => 'INT(11)',
                'description' => 'VARCHAR(255) NOT NULL',
                'order' => 'INT(8) NOT NULL DEFAULT 0',
                'ldap_json' => 'TEXT NULL',
                'active' => 'TINYINT(1) NOT NULL DEFAULT 1',
                'constraint inst_auth_site_fk FOREIGN KEY (site_id) REFERENCES site (id)',
                'constraint inst_auth_inst_fk FOREIGN KEY (institution_id) REFERENCES institution (id)',
                'constraint inst_auth_ldap_conf_fk FOREIGN KEY (ldap_config_id) REFERENCES ldap_config (id)',
                'constraint inst_auth_log_code_fk FOREIGN KEY (user_authentication_method) REFERENCES user_authentication_method (code)'
            ],
            true,
            'inst_auth'
        );
        $date = date("Y-m-d H:i:s");
        $this->createOETable('user_authentication',
            [
                'id' => 'pk',
                'institution_authentication_id' => 'INT(11)',
                'user_id' => 'INT(10) UNSIGNED NOT NULL',
                'username' => 'VARCHAR(40) NOT NULL',
                'password_hash' => 'VARCHAR(255) NULL',
                'password_salt' => 'VARCHAR(10) NULL',
                'password_softlocked_until' => 'datetime DEFAULT "' . $date . '"',
                'password_last_changed_date' => 'datetime DEFAULT "' . $date . '"',
                'password_failed_tries' => 'INT(10) DEFAULT 0',
                'password_status' => 'VARCHAR(10) DEFAULT "current"',
                'last_successful_login_date' => 'datetime NULL',
                'active' => 'TINYINT(1) NOT NULL DEFAULT 1',
                'constraint user_auth_user_fk FOREIGN KEY (user_id) REFERENCES user (id)',
                'constraint user_auth_inst_auth_fk FOREIGN KEY (institution_authentication_id) REFERENCES institution_authentication (id)'
            ],
            true,
            'user_auth'
        );
        $this->execute('INSERT INTO user_authentication_method(`code`) VALUES ("LOCAL"), ("LDAP")');

        // MIGRATE DATA TO NEW MODEL
        // CREATE NEW INSTITUTION AUTHENTICATION
        $default_inst_code = Yii::app()->params['institution_code'];

        if ($default_inst_code) {
            $default_institution = $this->dbConnection
                ->createCommand('SELECT * FROM institution WHERE remote_id = :remote_id')
                ->bindValues(array(':remote_id' => $default_inst_code))
                ->queryRow();
        } else {
            $default_institution = null;
        }
        if (!$default_institution) {
            throw new Exception("No default institution specified");
        }

        // CREATE LOCAL INSTITUTION AUTHENTICATION
        $this->insert('institution_authentication', array(
            'institution_id' => $default_institution['id'],
            'user_authentication_method' => 'LOCAL',
            'description' => "LOCAL authentication for {$default_institution['name']}."
        ));

        $local_inst_auth = $this->dbConnection->getLastInsertID();

        // DETERMINE WHICH USERS ARE LDAP AND WHICH ARE LOCAL
        if (Yii::app()->params['auth_source'] === 'BASIC') {
            // CREATE USER AUTHENTICATION ENTRIES FOR LOCAL USERS
            $this->execute("INSERT INTO `user_authentication`(`institution_authentication_id`,`user_id`,`username`,`password_hash`, `password_salt`) SELECT $local_inst_auth, u.id, u.username, u.password, u.salt FROM `user` u;");
        } else {
            $local_users = Yii::app()->params['local_users'] ?? [];
            $local_user_ids = $this->dbConnection
                ->createCommand("SELECT id FROM user WHERE username IN ('" . implode("','", $local_users) . "')")
                ->queryColumn();

            // DETERMINE LDAP CONFIGURATION
            // old param name => new param name
            $main_ldap_params = [
                'ldap_method' => 'ldap_method',
                'ldap_server' => 'ldap_server',
                'ldap_port' => 'ldap_port',
                'ldap_admin_dn' => 'ldap_admin_dn',
                'ldap_password' => 'ldap_admin_password',
                'ldap_dn' => 'ldap_dn'
            ];
            $ldap_params = [ 'ldap_additional_params' => [] ];
            foreach (array_keys(Yii::app()->params->toArray()) as $param_key) {
                if (substr($param_key, 0, 4) === 'ldap') {
                    if (array_key_exists($param_key, $main_ldap_params)) {
                        $ldap_params[$main_ldap_params[$param_key]] = Yii::app()->params[$param_key];
                    } else {
                        $ldap_params['ldap_additional_params'][] = [
                            'key' => $param_key,
                            'value' => Yii::app()->params[$param_key]
                        ];
                    }
                }
            }
            $ldap_json = json_encode($ldap_params);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception(json_last_error_msg());
            }
            // CREATE NEW LDAP CONFIG
            $this->insert('ldap_config', array(
                'ldap_json' => $ldap_json,
                'description' => "LDAP Config generated from local config",
            ));
            $ldap_config_id = $this->dbConnection->getLastInsertID();

            // CREATE LDAP INSTITUTION AUTHENTICATION
            $this->insert('institution_authentication', array(
                'institution_id' => $default_institution['id'],
                'user_authentication_method' => 'LDAP',
                'description' => "LDAP authentication for {$default_institution['name']}.",
                'ldap_config_id' => $ldap_config_id
            ));
            $ldap_inst_auth = $this->dbConnection->getLastInsertID();

            // CREATE USER AUTHENTICATION ENTRIES FOR LDAP USERS
            $this->execute("INSERT INTO `user_authentication`(`institution_authentication_id`,`user_id`,`username`,`active`) SELECT $ldap_inst_auth, u.id, u.username, u.active FROM `user` u WHERE u.id NOT IN (".implode(",", $local_user_ids).");");
            // CREATE USER AUTHENTICATION ENTRIES FOR LOCAL USERS
            $this->execute("INSERT INTO `user_authentication`(`institution_authentication_id`,`user_id`,`username`,`password_hash`, `password_salt`, `active`) SELECT $local_inst_auth, u.id, u.username, u.password, u.salt, u.active FROM `user` u WHERE u.id IN (".implode(",", $local_user_ids).");");
        }

        $special_usernames = Yii::app()->params['special_usernames'] ?? [];
        foreach ($special_usernames as $special_username) {
            $user = $this->dbConnection->createCommand('SELECT id, password, salt FROM user WHERE username = :username')
                ->bindValues(array(':username' => $special_username))
                ->queryRow();
            if (!$user) {
                $this->execute("INSERT INTO `user`(`username`,`first_name`,`last_name`,`email`,`active`,`global_firm_rights`,`title`,`qualifications`,`role`,`has_selected_firms`) VALUES ('$special_username','$special_username','user','info@abehr.com',1,1,'','','',0)");
                $user_id = $this->dbConnection->createCommand('SELECT id FROM user WHERE username = :username')
                    ->bindValues(array(':username' => $special_username))
                    ->queryScalar();
                //default password "P@ssw0rd" - this need to be changed when configuring the server
                $password_hash = '$2y$10$UXuBwd/CdkjES5I1xXV0D.ARQoKRVhwpaJNaNztpvvVKOLFIdOzbe';
                $password_salt = null;
            } else {
                $user_id = $user['id'];
                $password_hash = $user['password'];
                $password_salt = $user['salt'];
            }

            // Need to also specify appropriate roles
            $this->execute("INSERT INTO `user_authentication`(`institution_authentication_id`,`user_id`,`username`,`password_hash`, `password_salt`) VALUES (NULL, $user_id, '$special_username', '$password_hash', '$password_salt');");
        }

        // REMOVE LEGACY FIELDS - Uncomment when usages have been removed.
        $this->execute('ALTER TABLE `user` DROP COLUMN `username` , DROP COLUMN `password` , DROP COLUMN `salt` , DROP COLUMN `password_last_changed_date` , DROP COLUMN `password_failed_tries` , DROP COLUMN `password_status`, DROP COLUMN `password_softlocked_until`, DROP COLUMN `active`;');
        $this->execute('ALTER TABLE `user_version` DROP COLUMN `username` , DROP COLUMN `password` , DROP COLUMN `salt` , DROP COLUMN `password_last_changed_date` , DROP COLUMN `password_failed_tries` , DROP COLUMN `password_status`, DROP COLUMN `password_softlocked_until`, DROP COLUMN `active`;');
    }

    public function down()
    {
        echo "m200517_044325_add_multiple_LDAP_auth_to_institutions does not support migration down.\n";
        return false;
    }
}
