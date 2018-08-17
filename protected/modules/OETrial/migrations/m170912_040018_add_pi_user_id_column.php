<?php

class m170912_040018_add_pi_user_id_column extends OEMigration
{
	public function safeUp()
	{
	    $this->addColumn('trial', 'pi_user_id', 'int(10) unsigned NOT NULL'); // This will default to the same value as the owner_user_id but can be reassigned.')
        $this->addColumn('trial_version', 'pi_user_id', 'int(10) unsigned NOT NULL');
        $this->execute('UPDATE trial SET pi_user_id = owner_user_id');
        $this->execute('UPDATE trial_version SET pi_user_id = owner_user_id');
        $this->addForeignKey('pi_fk', 'trial', 'pi_user_id', 'user', 'id');
	}

	public function safeDown()
	{
	    $this->dropForeignKey('pi_fk', 'trial');
		$this->dropColumn('trial', 'pi_user_id');
	}
}