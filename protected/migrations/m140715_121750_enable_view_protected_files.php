
<?php

class m140715_121750_enable_view_protected_files extends OEMigration
{
	public function safeUp()
	{
		$this->update('authitemchild',array('parent' => 'View clinical'),"parent = 'Edit' and child = 'TaskViewProtectedFile'");
	}

	public function safeDown()
	{
		$this->update('authitemchild',array('parent' => 'Edit'),"parent = 'View clinical' and child = 'TaskViewProtectedFile'");
	}
}
