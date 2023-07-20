<?php

class m230718_042507_adding_index_to_active_columns extends CDbMigration
// In response to Admin-->Core-->Users page being slow to load.
{
    public function safeUp()
    {
        if ($this->dbConnection->createCommand("SHOW INDEX FROM user_authentication WHERE Key_name = 'idx_user_authentication_active'")->queryScalar()) {
            $this->execute("DROP INDEX idx_user_authentication_active ON user_authentication");
        }
        if ($this->dbConnection->createCommand("SHOW INDEX FROM firm WHERE Key_name = 'idx_firm_active'")->queryScalar()) {
            $this->execute("DROP INDEX idx_firm_active ON firm");
        }
        if ($this->dbConnection->createCommand("SHOW INDEX FROM institution_authentication WHERE Key_name = 'idx_institution_authentication_active'")->queryScalar()) {
            $this->execute("DROP INDEX idx_institution_authentication_active ON institution_authentication");
        }
        $this->execute("CREATE INDEX idx_user_authentication_active ON user_authentication(active)");
        $this->execute("CREATE INDEX idx_firm_active ON firm(active)");
        $this->execute("CREATE INDEX idx_institution_authentication_active ON institution_authentication(active)");
    }

    public function safeDown()
    {
        if ($this->dbConnection->createCommand("SHOW INDEX FROM user_authentication WHERE Key_name = 'idx_user_authentication_active'")->queryScalar()) {
            $this->execute("DROP INDEX idx_user_authentication_active ON user_authentication");
        }
        if ($this->dbConnection->createCommand("SHOW INDEX FROM firm WHERE Key_name = 'idx_firm_active'")->queryScalar()) {
            $this->execute("DROP INDEX idx_firm_active ON firm");
        }
        if ($this->dbConnection->createCommand("SHOW INDEX FROM institution_authentication WHERE Key_name = 'idx_institution_authentication_active'")->queryScalar()) {
            $this->execute("DROP INDEX idx_institution_authentication_active ON institution_authentication");
        }
    }
}
