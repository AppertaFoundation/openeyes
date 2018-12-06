<?php

class m181205_160624_create_icon_id_column_for_nhs_number_status extends CDbMigration
{
	public function up()
	{
	    $this->addColumn('nhs_number_verification_status' , 'icon_id' , 'int(11)');

	    $grey_icon_id = Icons::model()->find('class_name = ?', ['exclamation'])->id;
	    $amber_icon_id = Icons::model()->find('class_name = ?', ['exclamation-amber'])->id;
	    $green_icon_id = Icons::model()->find('class_name = ?', ['exclamation-green'])->id;
	    $red_icon_id = Icons::model()->find('class_name = ?', ['exclamation-red'])->id;

	    $this->update('nhs_number_verification_status' , ['icon_id' => $green_icon_id] , 'code = 01');
	    $this->update('nhs_number_verification_status' , ['icon_id' => $amber_icon_id] , 'code = 02');
	    $this->update('nhs_number_verification_status' , ['icon_id' => $red_icon_id] , 'code = 03');
	    $this->update('nhs_number_verification_status' , ['icon_id' => $red_icon_id] , 'code = 04');
	    $this->update('nhs_number_verification_status' , ['icon_id' => $red_icon_id] , 'code = 05');
	    $this->update('nhs_number_verification_status' , ['icon_id' => $amber_icon_id] , 'code = 06');
	    $this->update('nhs_number_verification_status' , ['icon_id' => $grey_icon_id] , 'code = 07');
	    $this->update('nhs_number_verification_status' , ['icon_id' => $grey_icon_id] , 'code = 08');
	}

	public function down()
	{
		$this->dropColumn('nhs_number_verification_status' , 'icon_id');
	}
}