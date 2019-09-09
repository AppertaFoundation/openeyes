<?php

class m190501_154249_migrate_inr_to_new_format extends CDbMigration
{
	public function up()
	{
	    $number_type_field = OphInLabResults_Field_Type::model()->find('name = "Numeric Field"');

	    $this->update('ophinlabresults_type' ,
            ['min_range' => 0.1 , 'max_range' => 50, 'show_on_whiteboard' => 1 , 'field_type_id' => $number_type_field->id],
            'type = :type' , [':type' => 'INR']
        );
	}

	public function down()
	{
		echo "Columns will be destroyed in the other migrations therefore the row will be in the state as before the migration";
	}
}