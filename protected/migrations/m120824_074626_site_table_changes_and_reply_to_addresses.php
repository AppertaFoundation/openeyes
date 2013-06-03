<?php

class m120824_074626_site_table_changes_and_reply_to_addresses extends CDbMigration
{
	public function up()
	{
		$this->addColumn('site','location','varchar(64) COLLATE utf8_bin NOT NULL');

		foreach (Yii::app()->db->createCommand()->select("*")->from("site")->where('institution_id=:one',array(':one'=>1))->queryAll() as $site) {
			$update = array();

			if (preg_match('/, (.*?)$/',$site['name'],$m)) {
				if ($m[1] == 'Harlequin Shopping Centre, Watford') {
					$m[1] = 'Watford';
				}

				$update['location'] = $m[1];
				$site['name'] = preg_replace('/, .*$/','',$site['name']);
			}

			$update['name'] = preg_replace('/^Moorfields at /','',$site['name']);
			$this->update('site',$update,"id={$site['id']}");
		}

		$this->addColumn('institution','short_name','varchar(64) COLLATE utf8_bin NOT NULL');

		$this->update('institution',array('short_name'=>'Moorfields'),'id=1');
	}

	public function down()
	{
		$this->dropColumn('institution','short_name');

		foreach (Yii::app()->db->createCommand()->select("*")->from("site")->where('institution_id=:one',array(':one'=>1))->queryAll() as $site) {
			$update = array();

			if ($site['location']) {
				if ($site['name'] == 'Boots Opticians' and $site['location'] == 'Watford') {
					$site['location'] = $update['location'] = 'Harlequin Shopping Centre, Watford';
				}
				$update['name'] = 'Moorfields at '.$site['name'].', '.$site['location'];
				$this->update('site',$update,"id={$site['id']}");
			}
		}

		$this->dropColumn('site','location');
	}
}
