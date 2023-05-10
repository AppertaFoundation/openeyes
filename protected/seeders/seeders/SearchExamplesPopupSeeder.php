<?php

namespace OE\seeders\seeders;

use OE\seeders\BaseSeeder;
use \Institution;
use \PatientIdentifierHelper;
use \SettingMetadata;

class SearchExamplesPopupSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $institution_id = $this->app_context->getSelectedInstitution();
        $site_id = \Yii::app()->session['selected_site_id'];

        $primary_identifier_prompt = PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $institution_id, $site_id);
        $secondary_identifier_prompt = PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(SettingMetadata::model()->getSetting('display_secondary_number_usage_code'), $institution_id, $site_id);

        $primary_pattern = PatientIdentifierHelper::getSearchExamplePatternBasedOnIdentifierType($primary_identifier_prompt);
        $secondary_pattern = PatientIdentifierHelper::getSearchExamplePatternBasedOnIdentifierType($secondary_identifier_prompt);

        return [
            'primaryPattern' => $primary_pattern,
            'secondaryPattern' => $secondary_pattern
        ];
    }
}
