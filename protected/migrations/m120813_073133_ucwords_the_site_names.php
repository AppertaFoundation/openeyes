<?php

class m120813_073133_ucwords_the_site_names extends CDbMigration
{
	public function up()
	{
		foreach (Site::model()->findAll('institution_id=1') as $site) {
			$site->name = str_replace(' At ',' at ',ucwords($site->name));
			$site->save();
		}
	}

	public function down()
	{
	}
}
