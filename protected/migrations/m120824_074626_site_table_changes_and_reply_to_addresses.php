<?php

class m120824_074626_site_table_changes_and_reply_to_addresses extends CDbMigration
{
	public function up()
	{
		$this->addColumn('site','location','varchar(64) COLLATE utf8_bin NOT NULL');

		foreach (Site::model()->findAll('institution_id=1') as $site) {
			if (preg_match('/, (.*?)$/',$site->name,$m)) {
				if ($m[1] == 'Harlequin Shopping Centre, Watford') {
					$m[1] = 'Watford';
				}

				$site->location = $m[1];
				$site->name = preg_replace('/, .*$/','',$site->name);
			}

			$site->name = preg_replace('/^Moorfields at /','',$site->name);
			$site->save();
		}

		$this->addColumn('institution','short_name','varchar(64) COLLATE utf8_bin NOT NULL');

		if (Institution::model()->findByPk(1)) {
			$this->update('institution',array('short_name'=>'Moorfields'),'id=1');
		}
	}

	public function down()
	{
		$this->dropColumn('institution','short_name');

		foreach (Site::model()->findAll('institution_id=1') as $site) {
			if ($site->location) {
				if ($site->name == 'Boots Opticians' and $site->location == 'Watford') {
					$site->location = 'Harlequin Shopping Centre, Watford';
				}
				$site->name = 'Moorfields at '.$site->name.', '.$site->location;
				$site->save();
			}
		}

		$this->dropColumn('site','location');
	}
}
