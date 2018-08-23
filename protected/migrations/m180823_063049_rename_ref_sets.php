<?php

class m180823_063049_rename_ref_sets extends CDbMigration
{
	public function up()
	{
	    /** @var RefSet[] $ref_sets */
	    $ref_sets = RefSet::model()->findAll();
	    foreach ($ref_sets as $ref_set) {
            if(!empty($ref_set->refSetRules)) {
                $new_name = "";
                $rule = $ref_set->refSetRules[0];
                if($rule->site_id) {
                    $new_name.= Site::model()->findByPk($rule->site_id)->name." ";
                }
                if($rule->subspecialty_id) {
                    $new_name.= Subspecialty::model()->findByPk($rule->subspecialty_id)->name." ";
                }

                $new_name.=$ref_set->name;
                $ref_set->name = $new_name;
                if(!$ref_set->save()) {
                    die("Couldnt save: ".print_r($ref_set->errors, true));
                }
            }
        }
	}

	public function down()
	{
		return true;
	}
}