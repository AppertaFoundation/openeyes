<?php

class m141125_125851_secondary_common_disorder_letter_macro_field extends CDbMigration
{
	public function up()
	{
		$this->addColumn('secondaryto_common_oph_disorder','letter_macro_text','varchar(255) not null');
		$this->addColumn('secondaryto_common_oph_disorder_version','letter_macro_text','varchar(255) not null');
	}

	public function down()
	{
		$this->dropColumn('secondaryto_common_oph_disorder','letter_macro_text');
		$this->dropColumn('secondaryto_common_oph_disorder_version','letter_macro_text');
	}
}
