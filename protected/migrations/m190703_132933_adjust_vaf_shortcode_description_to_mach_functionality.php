<?php

class m190703_132933_adjust_vaf_shortcode_description_to_mach_functionality extends CDbMigration
{
	public function up()
	{
	    $this->update('patient_shortcode', ['description' => 'Latest Visual acuity findings'], 'code="vaf"');
	}

	public function down()
	{
        $this->update('patient_shortcode', ['description' => 'Visual acuity findings from latest examination'], 'code=vaf');
	}
}